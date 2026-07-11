<?php

namespace App\Services;

use App\Models\MaintenanceRequest;
use App\Models\Kamar;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate financial report data
     */
    public function generateFinancialReport($owner, $data)
    {
        $start = Carbon::createFromDate($data['report_year'], $data['report_month'])->startOfMonth();
        $end = Carbon::createFromDate($data['end_year'] ?? $data['report_year'], $data['end_month'] ?? $data['report_month'])->endOfMonth();

        // Query ALL transactions for the month (like admin page) - with relationships
        $transactions = Transaksi::with(['tenant.occupiedRoom', 'room.roomType', 'paymentProofs'])
            ->whereBetween('created_at', [$start, $end])
            ->when($owner, function ($q) use ($owner) {
                return $q->where('owner_id', $owner->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate stats by status
        $pendingCount = $transactions->where('status', 'pending_verification')->count();
        $verifiedAdminCount = $transactions->where('status', 'verified_by_admin')->count();
        $verifiedOwnerCount = $transactions->where('status', 'verified_by_owner')->count();
        $rejectedCount = $transactions->whereIn('status', ['rejected_by_admin', 'rejected_by_owner', 'cancelled_by_tenant'])->count();

        // Revenue from verified transactions only
        $verifiedTransactions = $transactions->where('status', 'verified_by_owner');
        $grossRevenue = $verifiedTransactions->sum('final_amount');

        // Calculate OPEX (operational expenses) from maintenance
        $opexQuery = MaintenanceRequest::whereBetween('created_at', [$start, $end]);
        if ($owner) {
            $opexQuery->whereHas('room', function ($q) use ($owner) {
                $q->where('owner_id', $owner->id);
            });
        }
        $opex = $opexQuery->sum('estimated_cost');

        $noi = $grossRevenue - $opex;

        return [
            'gross_revenue' => $grossRevenue,
            'transaction_count' => $transactions->count(),
            'pending_count' => $pendingCount,
            'verified_admin_count' => $verifiedAdminCount,
            'verified_owner_count' => $verifiedOwnerCount,
            'rejected_count' => $rejectedCount,
            'opex_total' => $opex,
            'capex_total' => 0,
            'noi_total' => $noi,
            'noi_percentage' => $grossRevenue > 0 ? round(($noi / $grossRevenue) * 100, 2) : 0,
            'transactions' => $transactions,
        ];
    }

    /**
     * Generate room status report data
     */
    public function generateRoomStatusReport($owner, $data)
    {
        // Get ALL rooms with relationships
        $rooms = Kamar::with(['roomType', 'occupants.tenantProfile', 'occupants.tenantTransactions'])
            ->when($owner, function ($q) use ($owner) {
                return $q->where('owner_id', $owner->id);
            })
            ->orderBy('room_number')
            ->get();

        // Normalise each room's status to reflect real occupancy (pivot OR
        // legacy current_tenant_id) so the counts below agree with the MCP
        // tools and the dashboard instead of trusting the raw `status` column.
        $rooms->each(fn ($room) => $this->normaliseRoomStatus($room));

        $occupied = $rooms->where('status', 'occupied')->count();
        $available = $rooms->where('status', 'available')->count();
        $maintenance = $rooms->where('status', 'maintenance')->count();
        $total = $rooms->count();

        // Group by room type for detailed breakdown
        $roomsByType = $rooms->groupBy('tipe_kamar_id')->map(function ($typeRooms) {
            return [
                'total' => $typeRooms->count(),
                'occupied' => $typeRooms->where('status', 'occupied')->count(),
                'available' => $typeRooms->where('status', 'available')->count(),
                'maintenance' => $typeRooms->where('status', 'maintenance')->count(),
            ];
        });

        // Group by floor
        $roomsByFloor = $rooms->groupBy('floor_number')->map(function ($floorRooms) {
            return [
                'total' => $floorRooms->count(),
                'occupied' => $floorRooms->where('status', 'occupied')->count(),
                'available' => $floorRooms->where('status', 'available')->count(),
            ];
        });

        return [
            'total_rooms' => $total,
            'occupied' => $occupied,
            'available' => $available,
            'maintenance' => $maintenance,
            'occupancy_rate' => $total > 0 ? round(($occupied / $total) * 100, 2) : 0,
            'rooms_by_type' => $roomsByType,
            'rooms_by_floor' => $roomsByFloor,
            'kamar' => $rooms,
            // Added for Owner view compatibility if it still uses legacy keys
            'rooms_detail' => $roomsByType,
        ];
    }

    /**
     * Generate tenant report data
     */
    public function generateTenantReport($owner, $data)
    {
        $month = $data['report_month'];
        $year = $data['report_year'];

        // Get ALL tenants with their profiles and rooms
        $tenantsQuery = User::where('role', 'tenant')
            ->with(['tenantProfile', 'currentRoom.roomType', 'occupiedRoom.roomType', 'tenantTransactions']);



        // If owner is provided, we MUST filter.
        if ($owner) {
            // Filter tenants linked to this owner
            $tenantsQuery->where(function ($q) use ($owner) {
                // 1. Linked via Room (Active/History) or Transaction
                $q->whereHas('occupiedRoom', function ($sq) use ($owner) {
                    $sq->where('owner_id', $owner->id);
                })
                    ->orWhereHas('currentRoom', function ($sq) use ($owner) {
                        $sq->where('owner_id', $owner->id);
                    })
                    ->orWhereHas('historyRooms', function ($sq) use ($owner) {
                        $sq->where('owner_id', $owner->id);
                    })
                    ->orWhereHas('tenantTransactions', function ($sq) use ($owner) {
                        $sq->where('owner_id', $owner->id);
                    })
                    // 2. OR Include "Global Orphans" (No Room History at all)
                    // Assuming they belong to the system and thus the primary owner should see them
                    ->orWhere(function ($subQ) {
                        $subQ->doesntHave('occupiedRoom')
                            ->doesntHave('currentRoom')
                            ->doesntHave('historyRooms')
                            ->doesntHave('tenantTransactions');
                    });
            });
        }

        $tenants = $tenantsQuery->get();

        // Calculate stats like admin page
        $totalTenants = $tenants->count();

        // Active tenants (has room)
        $activeTenants = $tenants->filter(function ($tenant) {
            return $tenant->currentRoom || $tenant->occupiedRoom->isNotEmpty();
        })->count();

        // Inactive tenants (no room)
        $inactiveTenants = $totalTenants - $activeTenants;

        // Verification disabled - all tenants are considered verified
        $verifiedTenants = $totalTenants;

        // Payment status calculation
        $today = Carbon::now()->startOfDay();
        $reminderDays = 7;
        $reminderEndDate = Carbon::now()->addDays($reminderDays)->endOfDay();

        // Enrich tenants with status labels and sorting keys
        $tenants = $tenants->map(function ($tenant) use ($today, $reminderEndDate) {
            // 1. Has Room Check (Moved up)
            $tenant->has_room = $tenant->currentRoom || $tenant->occupiedRoom->isNotEmpty();

            // 2. Account Status (Depends on Has Room)
            $tenant->account_status_label = $tenant->has_room ? 'Aktif' : 'Tidak Aktif';

            // 3. Payment Status & Stats Calculation
            // RELY ON MODEL ACCESSOR to ensure consistency with Admin Tenant List
            // $tenant->payment_status_label is automatically available via User model

            // 4. Room Number
            $tenant->sort_room_number = $tenant->has_room
                ? ($tenant->currentRoom->room_number ?? $tenant->occupiedRoom->first()->room_number ?? '')
                : '';

            return $tenant;
        });

        // Recalculate counts based on the enriched logic to ensure consistency
        // Labels must match User::getPaymentStatusLabelAttribute() exactly
        $overdueCount = $tenants->filter(fn($t) => $t->payment_status_label === 'Telat Bayar')->count();
        $expiringSoonCount = $tenants->filter(fn($t) => $t->payment_status_label === 'Segera Habis')->count();
        $activePaymentCount = $tenants->filter(fn($t) => $t->payment_status_label === 'Lancar')->count();

        // SORTING: 1. Active (Has Room), 2. Room Number (Natural), 3. Name
        $tenants = $tenants->sort(function ($a, $b) {
            // Priority: Has Room 
            if ($a->has_room && !$b->has_room)
                return -1;
            if (!$a->has_room && $b->has_room)
                return 1;

            if ($a->has_room) {
                // Sort by Room Number
                return strnatcmp($a->sort_room_number, $b->sort_room_number);
            }
            // Fallback: Name
            return strcasecmp($a->name, $b->name);
        })->values();

        return [
            'tenant_count' => $totalTenants,
            'active_tenants' => $activeTenants,
            'inactive_tenants' => $inactiveTenants,
            'verified_count' => $verifiedTenants,
            'overdue_count' => $overdueCount,
            'expiring_soon' => $expiringSoonCount,
            'active_payment_count' => $activePaymentCount,
            'tenants' => $tenants,
        ];
    }

    /**
     * Generate Comprehensive Report (Tenant + Room + Transaction)
     */
    public function generateComprehensiveReport($owner, $data)
    {
        $start = Carbon::createFromDate($data['report_year'], $data['report_month'])->startOfMonth();
        $end = Carbon::createFromDate($data['end_year'] ?? $data['report_year'], $data['end_month'] ?? $data['report_month'])->endOfMonth();

        // Get tenants
        $tenantsQuery = User::where('role', 'tenant')
            ->with([
                'occupiedRoom.roomType',
                'currentRoom.roomType',
                'tenantTransactions' => function ($q) {
                    $q->latest();
                }
            ]);

        if ($owner) {
            $tenantsQuery->where(function ($q) use ($owner) {
                $q->whereHas('historyRooms', function ($sq) use ($owner) {
                    $sq->where('owner_id', $owner->id);
                })
                    ->orWhereHas('currentRoom', function ($sq) use ($owner) {
                        $sq->where('owner_id', $owner->id);
                    })
                    ->orWhereHas('currentRoom', function ($sq) use ($owner) {
                        $sq->where('owner_id', $owner->id);
                    })
                    ->orWhereHas('tenantTransactions', function ($sq) use ($owner) {
                        $sq->where('owner_id', $owner->id);
                    })
                    // Include Global Orphans here too
                    ->orWhere(function ($subQ) {
                        $subQ->doesntHave('occupiedRoom')
                            ->doesntHave('currentRoom')
                            ->doesntHave('historyRooms')
                            ->doesntHave('tenantTransactions');
                    });
            });
        }
        $tenants = $tenantsQuery->get();

        // Sorting: Has Room (Active) -> Room Number -> Name
        $tenants = $tenants->sort(function ($a, $b) {
            $aRoom = $a->activeRoom;
            $bRoom = $b->activeRoom;

            // 1. By Active Status (Active first)
            if ($aRoom && !$bRoom)
                return -1;
            if (!$aRoom && $bRoom)
                return 1;

            // 2. By Room Number (if both active)
            if ($aRoom && $bRoom) {
                return strnatcmp($aRoom->room_number, $bRoom->room_number);
            }

            // 3. By Name (fallback)
            return strcasecmp($a->name, $b->name);
        });

        // Calculate summary stats
        $totalActive = $tenants->filter(function ($user) {
            return $user->activeRoom !== null;
        })->count();

        // Payment statuses
        // Labels must match User::getPaymentStatusLabelAttribute() exactly:
        // 'Lancar', 'Telat Bayar', 'Segera Habis', '-'
        $paid = $tenants->filter(fn($t) => $t->payment_status_label === 'Lancar')->count();
        $late = $tenants->filter(fn($t) => $t->payment_status_label === 'Telat Bayar')->count();
        $due = $tenants->filter(fn($t) => $t->payment_status_label === 'Segera Habis')->count();

        // Room Stats
        $rooms = Kamar::withCount('occupants');
        if ($owner) {
            $rooms->where('owner_id', $owner->id);
        }
        $rooms = $rooms->get();
        $rooms->each(fn ($room) => $this->normaliseRoomStatus($room));

        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $availableRooms = $rooms->where('status', 'available')->count();
        $totalRooms = $rooms->count();

        return [
            'tenants' => $tenants,
            'total_active' => $totalActive,
            'room_stats' => [
                'total' => $totalRooms,
                'occupied' => $occupiedRooms,
                'available' => $availableRooms,
            ],
            // Added keys requested by Owner view
            'kamar' => $rooms,
            'status_counts' => [
                'paid' => $paid,
                'late' => $late,
                'due' => $due
            ]
        ];
    }

    /**
     * Normalise a room's in-memory `status` to reflect real occupancy
     * (active pivot occupant OR legacy current_tenant_id), consistent with
     * Kamar::scopeOccupied() and the MCP tools. In-memory only; not persisted.
     */
    private function normaliseRoomStatus($room): void
    {
        $isOccupied = ($room->relationLoaded('occupants') && $room->occupants->isNotEmpty())
            || (($room->occupants_count ?? 0) > 0)
            || $room->current_tenant_id !== null;

        $room->status = $isOccupied
            ? 'occupied'
            : ($room->status === 'maintenance' ? 'maintenance' : 'available');
    }
}
