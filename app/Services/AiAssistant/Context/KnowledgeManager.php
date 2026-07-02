<?php

namespace App\Services\AiAssistant\Context;

use App\Models\Transaksi;
use App\Models\Kamar;
use App\Models\TipeKamar;
use App\Models\User;
use App\Models\Pengeluaran;
use App\Models\MaintenanceRequest;
use App\Models\Notification;
use App\Models\PemilikKos;
use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * KnowledgeManager - Aggregates ALL business data for AI context
 * 
 * This class connects ALL relevant database tables to provide
 * comprehensive knowledge to the AI assistant.
 */
class KnowledgeManager
{
    protected int $ownerId;
    protected $businessSettings;

    /**
     * Set the owner context for all queries
     */
    public function setOwner(int $ownerId): self
    {
        $this->ownerId = $ownerId;
        $this->businessSettings = PemilikKos::where('owner_id', $ownerId)->first();
        return $this;
    }

    /**
     * Get the boarding house name
     */
    public function getBoardingHouseName(): string
    {
        return $this->businessSettings->boarding_house_name ?? 'Kost';
    }

    /**
     * Aggregates ALL context into a single string for AI consumption
     */
    public function getAggregatedContext(): string
    {
        $now = Carbon::now();
        $boardingName = $this->getBoardingHouseName();

        return <<<EOT
=== KONTEKS DATA BISNIS: {$boardingName} ===
Tanggal & Waktu Saat Ini: {$now->translatedFormat('l, d F Y H:i')} WIB

{$this->getBusinessOverview()}

{$this->getFinancialSummary()}

{$this->getOccupancyStatus()}

{$this->getTenantDirectory()}

{$this->getPaymentStatusBreakdown()}

{$this->getMaintenanceStatus()}

{$this->getExpensesSummary()}

{$this->getRecentNotifications()}

{$this->getAdminActivitySummary()}

=== AKHIR DATA ===
EOT;
    }

    /**
     * Business Overview - Key Metrics at a Glance
     */
    protected function getBusinessOverview(): string
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Room Statistics (using occupants relationship - SOURCE OF TRUTH)
        $totalRooms = Kamar::where('owner_id', $this->ownerId)->count();
        $occupiedRooms = Kamar::where('owner_id', $this->ownerId)->has('occupants')->count();
        $maintenanceRooms = Kamar::where('owner_id', $this->ownerId)->where('status', 'maintenance')->count();
        $availableRooms = Kamar::where('owner_id', $this->ownerId)
            ->doesntHave('occupants')
            ->where('status', '!=', 'maintenance')
            ->count();
        
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // Tenant Count (Active occupants)
        $totalTenants = DB::table('riwayat_penghuni_kamar')
            ->join('kamar', 'riwayat_penghuni_kamar.kamar_id', '=', 'kamar.id')
            ->where('kamar.owner_id', $this->ownerId)
            ->whereNull('riwayat_penghuni_kamar.check_out_date')
            ->distinct('riwayat_penghuni_kamar.user_id')
            ->count('riwayat_penghuni_kamar.user_id');

        // Financial Metrics (Current Month)
        $totalIncome = Transaksi::where('owner_id', $this->ownerId)
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('status', 'verified_by_owner')
            ->sum('final_amount');

        $totalExpenses = Pengeluaran::where('owner_id', $this->ownerId)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        // Add maintenance costs
        $maintenanceCosts = MaintenanceRequest::whereHas('room', function($q) {
            $q->where('owner_id', $this->ownerId);
        })
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'completed')
            ->sum('estimated_cost');

        $totalExpenses += $maintenanceCosts;
        $netProfit = $totalIncome - $totalExpenses;

        // Pending Income
        $pendingIncome = Transaksi::where('owner_id', $this->ownerId)
            ->where('status', 'verified_by_admin')
            ->sum('amount');

        return <<<EOT
### 1. RINGKASAN BISNIS (Bulan Ini: {$now->translatedFormat('F Y')})
| Metrik | Nilai |
|--------|-------|
| Total Kamar | {$totalRooms} unit |
| Kamar Terisi | {$occupiedRooms} unit ({$occupancyRate}%) |
| Kamar Kosong | {$availableRooms} unit |
| Kamar Maintenance | {$maintenanceRooms} unit |
| Total Penghuni Aktif | {$totalTenants} orang |
| Pemasukan Bulan Ini | Rp {$this->formatMoney($totalIncome)} |
| Pengeluaran Bulan Ini | Rp {$this->formatMoney($totalExpenses)} |
| Laba Bersih Bulan Ini | Rp {$this->formatMoney($netProfit)} |
| Pembayaran Pending (Menunggu Verifikasi Owner) | Rp {$this->formatMoney($pendingIncome)} |
EOT;
    }

    /**
     * Financial Summary - Detailed Income & Transactions
     */
    protected function getFinancialSummary(): string
    {
        $transactions = Transaksi::with(['tenant:id,name', 'room:id,room_number'])
            ->where('owner_id', $this->ownerId)
            ->where('created_at', '>=', now()->subMonths(3))
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        if ($transactions->isEmpty()) {
            return "### 2. TRANSAKSI TERBARU\nBelum ada transaksi tercatat dalam 3 bulan terakhir.";
        }

        $lines = ["### 2. TRANSAKSI TERBARU (3 Bulan Terakhir, Maks 30 Data)"];
        $lines[] = "| Tanggal | Penyewa | Kamar | Jumlah | Periode | Status |";
        $lines[] = "|---------|---------|-------|--------|---------|--------|";

        foreach ($transactions as $t) {
            $date = $t->payment_date ? $t->payment_date->format('d/m/Y') : $t->created_at->format('d/m/Y');
            $tenant = $t->tenant?->name ?? 'Unknown';
            $room = $t->room?->room_number ?? '-';
            $amount = $this->formatMoney($t->final_amount ?? $t->amount);
            $period = $t->period_start_date && $t->period_end_date 
                ? $t->period_start_date->format('d/m') . ' - ' . $t->period_end_date->format('d/m/Y')
                : '-';
            $status = $this->translateStatus($t->status);
            $lines[] = "| {$date} | {$tenant} | {$room} | {$amount} | {$period} | {$status} |";
        }

        // Monthly Summary
        $monthlySummary = Transaksi::selectRaw('MONTH(payment_date) as month, YEAR(payment_date) as year, SUM(final_amount) as total')
            ->where('owner_id', $this->ownerId)
            ->where('status', 'verified_by_owner')
            ->where('payment_date', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        if ($monthlySummary->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "**Ringkasan Pemasukan per Bulan:**";
            foreach ($monthlySummary as $m) {
                $monthName = Carbon::createFromDate($m->year, $m->month, 1)->translatedFormat('F Y');
                $lines[] = "- {$monthName}: Rp {$this->formatMoney($m->total)}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Occupancy Status - All Rooms with Details
     */
    protected function getOccupancyStatus(): string
    {
        $rooms = Kamar::with(['roomType:id,name,capacity,price_per_month', 'occupants:id,name'])
            ->where('owner_id', $this->ownerId)
            ->orderBy('floor_number')
            ->orderBy('room_number')
            ->get();

        if ($rooms->isEmpty()) {
            return "### 3. STATUS KAMAR\nBelum ada kamar terdaftar.";
        }

        $lines = ["### 3. STATUS KAMAR (Semua Kamar)"];
        $lines[] = "| No Kamar | Lantai | Tipe | Kapasitas | Penghuni | Status | Harga/Bulan |";
        $lines[] = "|----------|--------|------|-----------|----------|--------|-------------|";

        foreach ($rooms as $room) {
            $occupantNames = $room->occupants->pluck('name')->join(', ') ?: '-';
            $occupantCount = $room->occupants->count();
            $capacity = $room->roomType->capacity ?? 1;
            $capacityText = "{$occupantCount}/{$capacity}";
            
            $status = $room->status === 'maintenance' ? '🔧 Maintenance' 
                : ($occupantCount >= $capacity ? '🔴 Penuh' 
                : ($occupantCount > 0 ? '🟡 Terisi Sebagian' : '🟢 Kosong'));
            
            $price = $this->formatMoney($room->price_per_month ?? $room->roomType->price_per_month ?? 0);
            $typeName = $room->roomType->name ?? '-';

            $lines[] = "| {$room->room_number} | {$room->floor_number} | {$typeName} | {$capacityText} | {$occupantNames} | {$status} | {$price} |";
        }

        // Room Types Summary
        $roomTypes = TipeKamar::where('owner_id', $this->ownerId)->where('status', 'active')->get();
        if ($roomTypes->isNotEmpty()) {
            $lines[] = "";
            $lines[] = "**Tipe Kamar Tersedia:**";
            foreach ($roomTypes as $rt) {
                $facilities = $this->formatFacilities($rt->facilities);
                $lines[] = "- **{$rt->name}**: Kapasitas {$rt->capacity} orang, Rp {$this->formatMoney($rt->price_per_month)}/bulan. Fasilitas: {$facilities}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Tenant Directory - All Active Tenants
     */
    protected function getTenantDirectory(): string
    {
        // Get tenants via riwayat_penghuni_kamar (SOURCE OF TRUTH)
        $tenantIds = DB::table('riwayat_penghuni_kamar')
            ->join('kamar', 'riwayat_penghuni_kamar.kamar_id', '=', 'kamar.id')
            ->where('kamar.owner_id', $this->ownerId)
            ->whereNull('riwayat_penghuni_kamar.check_out_date')
            ->pluck('riwayat_penghuni_kamar.user_id');

        $tenants = User::with(['tenantProfile', 'occupiedRoom', 'tenantTransactions' => function($q) {
            $q->where('status', 'verified_by_owner')->orderByDesc('period_end_date');
        }])
            ->whereIn('id', $tenantIds)
            ->get();

        if ($tenants->isEmpty()) {
            return "### 4. DIREKTORI PENYEWA AKTIF\nBelum ada penyewa terdaftar.";
        }

        $lines = ["### 4. DIREKTORI PENYEWA AKTIF"];
        $lines[] = "| Nama | No HP | Kamar | Check-in | Kontrak Berakhir | Status Bayar |";
        $lines[] = "|------|-------|-------|----------|------------------|--------------|";

        foreach ($tenants as $tenant) {
            $phone = $tenant->tenantProfile?->phone ?? '-';
            $room = $tenant->activeRoom?->room_number ?? '-';
            $checkIn = $tenant->activeRoom?->pivot?->check_in_date 
                ? Carbon::parse($tenant->activeRoom->pivot->check_in_date)->format('d/m/Y') 
                : '-';
            
            $lastTrx = $tenant->tenantTransactions->first();
            $contractEnd = $lastTrx?->period_end_date?->format('d/m/Y') ?? '-';
            $paymentStatus = $tenant->payment_status_label;
            
            $statusEmoji = match($paymentStatus) {
                'Lancar' => '✅',
                'Mau Habis' => '⚠️',
                'Nunggak' => '❌',
                default => '➖'
            };

            $lines[] = "| {$tenant->name} | {$phone} | {$room} | {$checkIn} | {$contractEnd} | {$statusEmoji} {$paymentStatus} |";
        }

        return implode("\n", $lines);
    }

    /**
     * Payment Status Breakdown - Categorized Tenants
     */
    protected function getPaymentStatusBreakdown(): string
    {
        $tenantIds = DB::table('riwayat_penghuni_kamar')
            ->join('kamar', 'riwayat_penghuni_kamar.kamar_id', '=', 'kamar.id')
            ->where('kamar.owner_id', $this->ownerId)
            ->whereNull('riwayat_penghuni_kamar.check_out_date')
            ->pluck('riwayat_penghuni_kamar.user_id');

        $tenants = User::with(['tenantTransactions' => function($q) {
            $q->where('status', 'verified_by_owner')->orderByDesc('period_end_date');
        }])->whereIn('id', $tenantIds)->get();

        $lancar = [];
        $mauHabis = [];
        $nunggak = [];

        $today = now()->startOfDay();
        $reminderDays = $this->businessSettings->invoice_reminder_days_before ?? 7;
        $reminderEnd = now()->addDays($reminderDays)->endOfDay();

        foreach ($tenants as $tenant) {
            $lastTrx = $tenant->tenantTransactions->first();
            if (!$lastTrx) {
                $nunggak[] = $tenant->name;
                continue;
            }

            $periodEnd = Carbon::parse($lastTrx->period_end_date);
            if ($periodEnd < $today) {
                $nunggak[] = $tenant->name . " (Habis: {$periodEnd->format('d/m/Y')})";
            } elseif ($periodEnd <= $reminderEnd) {
                $daysLeft = $today->diffInDays($periodEnd);
                $mauHabis[] = $tenant->name . " (H-{$daysLeft})";
            } else {
                $lancar[] = $tenant->name;
            }
        }

        $lines = ["### 5. STATUS PEMBAYARAN PENYEWA"];
        $lines[] = "";
        $lines[] = "**✅ Lancar (" . count($lancar) . " orang):** " . (empty($lancar) ? '-' : implode(', ', $lancar));
        $lines[] = "";
        $lines[] = "**⚠️ Mau Habis (" . count($mauHabis) . " orang):** " . (empty($mauHabis) ? '-' : implode(', ', $mauHabis));
        $lines[] = "";
        $lines[] = "**❌ Nunggak (" . count($nunggak) . " orang):** " . (empty($nunggak) ? '-' : implode(', ', $nunggak));

        return implode("\n", $lines);
    }

    /**
     * Maintenance Status - Active Issues
     */
    protected function getMaintenanceStatus(): string
    {
        $issues = MaintenanceRequest::with(['requestedBy:id,name', 'room:id,room_number'])
            ->whereHas('room', function($q) {
                $q->where('owner_id', $this->ownerId);
            })
            ->where('status', '!=', 'completed')
            ->orderByRaw("FIELD(urgency_level, 'high', 'medium', 'low')")
            ->get();

        if ($issues->isEmpty()) {
            return "### 6. LAPORAN MAINTENANCE\n✅ Tidak ada isu maintenance aktif. Semua dalam kondisi baik.";
        }

        $lines = ["### 6. LAPORAN MAINTENANCE AKTIF"];
        $lines[] = "| Prioritas | Kamar | Masalah | Pelapor | Status | Est. Biaya |";
        $lines[] = "|-----------|-------|---------|---------|--------|------------|";

        foreach ($issues as $issue) {
            $priority = match($issue->urgency_level) {
                'high' => '🔴 Tinggi',
                'medium' => '🟡 Sedang',
                default => '🟢 Rendah'
            };
            $room = $issue->room?->room_number ?? 'Umum';
            $reporter = $issue->requestedBy?->name ?? 'System';
            $statusText = ucfirst($issue->status);
            $cost = $issue->estimated_cost ? $this->formatMoney($issue->estimated_cost) : '-';

            $lines[] = "| {$priority} | {$room} | {$issue->title} | {$reporter} | {$statusText} | {$cost} |";
        }

        return implode("\n", $lines);
    }

    /**
     * Expenses Summary - OPEX/CAPEX
     */
    protected function getExpensesSummary(): string
    {
        $now = Carbon::now();
        
        // Current month expenses
        $expenses = Pengeluaran::where('owner_id', $this->ownerId)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->orderBy('date', 'desc')
            ->get();

        // Category breakdown
        $byCategory = Pengeluaran::selectRaw('category, SUM(amount) as total')
            ->where('owner_id', $this->ownerId)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->groupBy('category')
            ->get();

        // Type breakdown (OPEX vs CAPEX)
        $byType = Pengeluaran::selectRaw('type, SUM(amount) as total')
            ->where('owner_id', $this->ownerId)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->groupBy('type')
            ->get();

        $lines = ["### 7. PENGELUARAN BULAN INI ({$now->translatedFormat('F Y')})"];
        
        if ($byType->isNotEmpty()) {
            $lines[] = "**Breakdown per Tipe:**";
            foreach ($byType as $t) {
                $typeName = $t->type === 'opex' ? 'OPEX (Operasional)' : 'CAPEX (Aset)';
                $lines[] = "- {$typeName}: Rp {$this->formatMoney($t->total)}";
            }
            $lines[] = "";
        }

        if ($byCategory->isNotEmpty()) {
            $lines[] = "**Breakdown per Kategori:**";
            foreach ($byCategory as $c) {
                $categoryName = $this->translateCategory($c->category);
                $lines[] = "- {$categoryName}: Rp {$this->formatMoney($c->total)}";
            }
            $lines[] = "";
        }

        if ($expenses->isEmpty()) {
            $lines[] = "Belum ada pengeluaran tercatat bulan ini.";
        } else {
            $lines[] = "**Detail Pengeluaran Terbaru:**";
            $lines[] = "| Tanggal | Kategori | Deskripsi | Jumlah |";
            $lines[] = "|---------|----------|-----------|--------|";
            foreach ($expenses->take(10) as $e) {
                $date = $e->date->format('d/m/Y');
                $category = $this->translateCategory($e->category);
                $desc = $e->description ?: '-';
                $amount = $this->formatMoney($e->amount);
                $lines[] = "| {$date} | {$category} | {$desc} | {$amount} |";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Recent Notifications - For context awareness
     */
    protected function getRecentNotifications(): string
    {
        $notifications = Notification::where('user_id', $this->ownerId)
            ->where('status', '!=', 'archived')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($notifications->isEmpty()) {
            return "### 8. NOTIFIKASI TERBARU\nTidak ada notifikasi baru.";
        }

        $lines = ["### 8. NOTIFIKASI TERBARU"];
        foreach ($notifications as $notif) {
            $time = $notif->created_at->diffForHumans();
            $status = $notif->status === 'unread' ? '🔔' : '✓';
            $lines[] = "- {$status} [{$time}] {$notif->title}: {$notif->message}";
        }

        return implode("\n", $lines);
    }

    /**
     * Admin Activity Summary
     */
    protected function getAdminActivitySummary(): string
    {
        $logs = AdminActivityLog::with('admin:id,name')
            ->latest()
            ->limit(10)
            ->get();

        if ($logs->isEmpty()) {
            return "### 9. AKTIVITAS ADMIN TERBARU\nBelum ada aktivitas admin tercatat.";
        }

        $lines = ["### 9. AKTIVITAS ADMIN TERBARU"];
        foreach ($logs as $log) {
            $time = $log->created_at->diffForHumans();
            $admin = $log->admin?->name ?? 'System';
            $action = $log->activity_label ?? $log->action;
            $lines[] = "- [{$time}] {$admin}: {$action}";
        }

        return implode("\n", $lines);
    }

    // ==================== HELPER METHODS ====================

    protected function formatMoney($amount): string
    {
        return number_format($amount ?? 0, 0, ',', '.');
    }

    protected function translateStatus(string $status): string
    {
        return match($status) {
            'pending_verification' => '⏳ Pending',
            'verified_by_admin' => '🔍 Dicek Admin',
            'verified_by_owner' => '✅ Lunas',
            'rejected_by_admin' => '❌ Ditolak Admin',
            'rejected_by_owner' => '❌ Ditolak Owner',
            default => $status
        };
    }

    protected function translateCategory(?string $category): string
    {
        return match($category) {
            'electricity' => '⚡ Listrik',
            'water' => '💧 Air',
            'internet' => '🌐 Internet',
            'cleaning' => '🧹 Kebersihan',
            'maintenance' => '🔧 Maintenance',
            'marketing' => '📢 Marketing',
            'salary' => '💰 Gaji Staff',
            'furniture' => '🪑 Furniture',
            'electronics' => '📺 Elektronik',
            'renovation' => '🏗️ Renovasi',
            'other' => '📦 Lainnya',
            default => $category ?? '-'
        };
    }

    /**
     * Safely format facilities array to string
     */
    protected function formatFacilities($facilities): string
    {
        if (empty($facilities)) {
            return '-';
        }

        if (is_string($facilities)) {
            return $facilities;
        }

        if (is_array($facilities)) {
            // Flatten if nested array
            $flat = [];
            foreach ($facilities as $item) {
                if (is_array($item)) {
                    // If it's an array with 'name' key (e.g., [{name: 'AC'}, ...])
                    if (isset($item['name'])) {
                        $flat[] = $item['name'];
                    } else {
                        // Otherwise flatten recursively
                        $flat = array_merge($flat, array_map('strval', $item));
                    }
                } else {
                    $flat[] = (string) $item;
                }
            }
            return implode(', ', $flat);
        }

        return (string) $facilities;
    }
}

