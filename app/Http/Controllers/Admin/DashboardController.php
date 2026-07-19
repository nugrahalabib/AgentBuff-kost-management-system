<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $ownerId = auth()->user()->ownerId(); // Admin harus di-filter by owner

        // Total Penghuni (Active Tenants - matches TenantController logic)
        $totalTenants = User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn($q) => $q->where('owner_id', $ownerId))
            ->where(function ($q) {
                $q->whereHas('currentRoom')->orWhereHas('occupiedRoom');
            })
            ->count();

        // Kamar Terisi (Occupied Rooms - matches RoomController status='occupied' logic)
        $occupiedRooms = Kamar::where('owner_id', $ownerId)->has('occupants')->count();

        // Kamar Tersedia (Available Rooms - Empty Rooms, matches RoomController status='available' logic)
        $availableRooms = Kamar::where('owner_id', $ownerId)->doesntHave('occupants')->where('status', '!=', 'maintenance')->count();

        // Jatuh Tempo (Overdue + Expiring Soon)
        $ownerUser = auth()->user()->resolveOwner();
        $reminderDays = $ownerUser?->businessSettings?->invoice_reminder_days_before ?? 7;

        // 1. Get Tenants who are OVERDUE (Nunggak)
        $overdueTenants = Transaksi::where('owner_id', $ownerId)
            ->where('status', 'verified_by_owner')
            ->where('period_end_date', '<', Carbon::now()->startOfDay())
            // Ensure they haven't renewed (no future transaction)
            ->whereNotExists(function ($query) use ($ownerId) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('transaksi as t2')
                    ->whereColumn('t2.penyewa_id', 'transaksi.penyewa_id')
                    ->where('t2.owner_id', $ownerId)
                    ->where('t2.status', 'verified_by_owner')
                    ->where('t2.period_end_date', '>=', Carbon::now()->startOfDay());
            })
            // Get the latest transaction for each tenant to check the date
            ->whereIn('id', function ($q) use ($ownerId) {
                $q->select(\Illuminate\Support\Facades\DB::raw('MAX(id)'))
                    ->from('transaksi')
                    ->where('owner_id', $ownerId)
                    ->where('status', 'verified_by_owner')
                    ->groupBy('penyewa_id');
            })
            ->get();

        // 2. Get Tenants who are EXPIRING SOON (Mau Habis)
        $expiringTenants = Transaksi::where('owner_id', $ownerId)
            ->where('status', 'verified_by_owner')
            ->whereBetween('period_end_date', [
                Carbon::now()->startOfDay(),
                Carbon::now()->addDays($reminderDays)->endOfDay()
            ])
            // Ensure this is their latest active transaction
            ->whereIn('id', function ($q) use ($ownerId) {
                $q->select(\Illuminate\Support\Facades\DB::raw('MAX(id)'))
                    ->from('transaksi')
                    ->where('owner_id', $ownerId)
                    ->where('status', 'verified_by_owner')
                    ->groupBy('penyewa_id');
            })
            ->get();

        // Merge collections
        $allDue = $overdueTenants->merge($expiringTenants);

        // Count for the Card
        $dueSoon = $allDue->unique('penyewa_id')->count();

        // Detailed List for the Sidebar
        $dueTenantsDetails = $allDue->unique('penyewa_id')
            ->sortBy('period_end_date') // Sort by date (Oldest/Most Overdue first)
            ->take(5)
            ->map(function ($transaction) {
                $daysUntilDue = (int) now()->diffInDays($transaction->period_end_date, false);
                $isOverdue = $daysUntilDue < 0;

                return [
                    'tenant_name' => $transaction->tenant->name ?? 'Unknown',
                    'due_date' => $transaction->period_end_date,
                    'days_until_due' => abs($daysUntilDue),
                    'status_color' => $isOverdue ? 'red' : 'yellow', // Red for overdue, Yellow for soon
                    'status_label' => $isOverdue ? 'Telat ' . abs($daysUntilDue) . ' Hari' : 'Habis dalam ' . abs($daysUntilDue) . ' Hari',
                    'status_icon_text' => $isOverdue ? '!' : abs($daysUntilDue),
                    'amount' => $transaction->amount,
                ];
            });

        // Notifikasi Terbaru (latest 3)
        $latestNotifications = Notification::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Okupansi (operasional — admin TIDAK melihat data keuangan kos, itu hak owner).
        $totalKamar = Kamar::where('owner_id', $ownerId)->count();
        $kamarMaintenance = Kamar::where('owner_id', $ownerId)->where('status', 'maintenance')->count();
        $occupancyRate = $totalKamar > 0 ? round(($occupiedRooms / $totalKamar) * 100) : 0;

        // Penyewa terbaru (tanpa nilai uang).
        $recentTenants = User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn ($q) => $q->where('owner_id', $ownerId))
            ->with(['tenantProfile', 'occupiedRoom'])
            ->orderByDesc('created_at')->limit(6)->get();

        return view('admin.dashboard', [
            'totalPenyewa' => $totalTenants,
            'kamarTersedia' => $availableRooms,
            'kamarTerisi' => $occupiedRooms,
            'dueSoon' => $dueSoon,
            'detailPenyewaJatuhTempo' => $dueTenantsDetails,
            'latestNotifications' => $latestNotifications,
            'occupancyRate' => $occupancyRate,
            'totalKamar' => $totalKamar,
            'kamarMaintenance' => $kamarMaintenance,
            'recentTenants' => $recentTenants,
        ]);
    }
}
