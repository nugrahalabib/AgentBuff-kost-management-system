<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Notification;
use App\Models\FinancialReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show owner dashboard
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $owner = Auth::user();
        $now = Carbon::now();

        // Support month/year navigation from request
        $currentMonth = $request->input('month', $now->month);
        $currentYear = $request->input('year', $now->year);

        // 1. FINANCIAL CARDS (Current Month Snapshot, matching ReportController)

        // Total Pemasukan (Current Month)
        $totalIncome = Transaksi::where('owner_id', $owner->id)
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('status', 'verified_by_owner')
            ->sum('final_amount');

        // Pemasukan Sementara (Pending Verification) - All Time Pending
        $pendingIncome = Transaksi::where('owner_id', $owner->id)
            ->where('status', 'verified_by_admin')
            ->sum('amount');

        // Pemasukan Bulan Lalu (for Growth %)
        $lastMonthDate = $now->copy()->subMonth();
        $lastMonthIncome = Transaksi::where('owner_id', $owner->id)
            ->whereMonth('payment_date', $lastMonthDate->month)
            ->whereYear('payment_date', $lastMonthDate->year)
            ->where('status', 'verified_by_owner')
            ->sum('final_amount');

        // Growth = +100% when last month had no income but this month has some;
        // 0 when both months are zero (no data) so the UI doesn't claim growth out of nothing.
        $incomeGrowthPercent = $lastMonthIncome > 0
            ? round((($totalIncome - $lastMonthIncome) / $lastMonthIncome) * 100)
            : ($totalIncome > 0 ? 100 : 0);

        // Total Pengeluaran (Current Month) - Includes Maintenance + Manual Expenses
        $manualExpenses = \App\Models\Pengeluaran::where('owner_id', $owner->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $maintenanceExpenses = \App\Models\MaintenanceRequest::whereHas('room', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'completed')
            ->sum('estimated_cost');

        $totalExpenses = $manualExpenses + $maintenanceExpenses;
        $expensePercent = $totalIncome > 0 ? round(($totalExpenses / $totalIncome) * 100) : 0;

        // Laba Bersih
        $netProfit = $totalIncome - $totalExpenses;

        // Proyeksi Bulan Depan
        // Logic: Full Potential Revenue - Current Month's Expenses (as proxy)
        $rooms = Kamar::with('roomType')->where('owner_id', $owner->id)->get();
        $grossPotential = $rooms->reduce(function ($carry, $room) {
            $price = $room->price_per_month ?? $room->roomType->price_per_month ?? 0;
            return $carry + $price;
        }, 0);

        $projectedNext = max(0, $grossPotential - $totalExpenses);

        // 2. CASHFLOW ANALYSIS (12 Months Data)
        $incomeData = [];
        $expenseData = [];
        $months = range(1, 12);

        // Optimize with grouping if dataset is large, but loop is fine for single owner
        foreach ($months as $m) {
            $incomeData[] = Transaksi::where('owner_id', $owner->id)
                ->whereMonth('payment_date', $m)
                ->whereYear('payment_date', $currentYear)
                ->where('status', 'verified_by_owner')
                ->sum('final_amount');

            $mExpense = \App\Models\Pengeluaran::where('owner_id', $owner->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $currentYear)
                ->sum('amount');

            $mMaint = \App\Models\MaintenanceRequest::whereHas('room', function ($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })
                ->whereMonth('created_at', $m)
                ->whereYear('created_at', $currentYear)
                ->where('status', 'completed')
                ->sum('estimated_cost');

            $expenseData[] = $mExpense + $mMaint;
        }

        // 3. ROOM STATUS (Logic matching Owner\RoomController)
        $totalRooms = Kamar::where('owner_id', $owner->id)->count();
        $occupiedRooms = Kamar::where('owner_id', $owner->id)->has('occupants')->count();
        $maintenanceRooms = Kamar::where('owner_id', $owner->id)->where('status', 'maintenance')->count();
        // Available = Empty (No occupants) AND Not under maintenance
        $availableRooms = Kamar::where('owner_id', $owner->id)
            ->doesntHave('occupants')
            ->where('status', '!=', 'maintenance')
            ->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // 4. ACTIVITY LOGS (Admin Activity)
        $logs = \App\Models\AdminActivityLog::with('admin')
            ->latest()
            ->limit(5)
            ->get();

        // 5. RECENT TENANTS
        // Fetch tenants linked to this owner (Current Room OR History)
        $recentTenants = User::where('role', 'tenant')
            ->where(function ($q) use ($owner) {
                // Must be currently in a room owned by this owner
                $q->whereHas('currentRoom', function ($sq) use ($owner) {
                    $sq->where('owner_id', $owner->id);
                })
                    // OR has occupied a room in the past (active transaction history generally covers this)
                    ->orWhereHas('occupiedRoom', function ($sq) use ($owner) {
                    $sq->where('owner_id', $owner->id);
                });
            })
            ->with([
                'currentRoom',
                'occupiedRoom',
                'tenantTransactions' => function ($query) {
                    $query->latest('created_at');
                }
            ])
            ->latest('created_at') // Newest tenants first
            ->limit(5)
            ->get();

        return view('pemilik-kos.dashboard', [
            'totalIncome' => $totalIncome,
            'pendingIncome' => $pendingIncome,
            'incomeGrowthPercent' => $incomeGrowthPercent,
            'totalExpenses' => $totalExpenses,
            'expensePercent' => $expensePercent,
            'netProfit' => $netProfit,
            'projectedNext' => $projectedNext,
            'totalKamar' => $totalRooms,
            'kamarTerisi' => $occupiedRooms,
            'kamarTersedia' => $availableRooms,
            'kamarMaintenance' => $maintenanceRooms,
            'occupancyRate' => $occupancyRate,
            'logs' => $logs,
            'penyewaTerbaru' => $recentTenants,
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }
}
