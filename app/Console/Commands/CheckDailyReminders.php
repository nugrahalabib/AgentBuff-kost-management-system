<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use App\Models\Kamar;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class CheckDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring contracts and long-empty rooms to notify owner.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily reminder check...');

        $owner = User::where('role', 'owner')->first();
        if (!$owner) {
            $this->error('No owner found.');
            return;
        }

        $this->checkExpiringContracts($owner);
        $this->checkEmptyRooms($owner);

        $this->info('Daily reminder check completed.');
    }

    private function checkExpiringContracts($owner)
    {
        $today = Carbon::today();
        $targetDays = [7, 30];

        foreach ($targetDays as $days) {
            $targetDate = $today->copy()->addDays($days);
            
            // Find active transactions expiring on target date
            $expiringTransactions = Transaksi::whereDate('period_end_date', $targetDate)
                ->where('status', 'verified_by_owner')
                ->with(['tenant', 'room'])
                ->get();

            foreach ($expiringTransactions as $transaction) {
                // Check if notification already sent today for this transaction
                $exists = Notification::where('type', 'contract_expiring')
                    ->where('related_entity_id', $transaction->id)
                    ->whereDate('created_at', $today)
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'user_id' => $owner->id,
                        'type' => 'contract_expiring',
                        'category' => 'system',
                        'title' => "Kontrak Akan Habis ({$days} Hari)",
                        'message' => "Kontrak penyewa {$transaction->tenant->name} di Kamar " . ($transaction->room->room_number ?? 'Unknown') . " akan habis pada {$targetDate->format('d M Y')}.",
                        'related_entity_type' => 'transaction',
                        'related_entity_id' => $transaction->id,
                        'priority' => $days === 7 ? 'high' : 'medium', // Higher priority for H-7
                        'action_required' => true, // Owner needs to decide (extend/terminate)
                    ]);
                    
                    $this->info("Notified owner about expiring contract: {$transaction->tenant->name} (H-{$days})");
                }
            }
        }
    }

    private function checkEmptyRooms($owner)
    {
        // Find rooms with NO current occupants and status is 'available'
        // 'doesntHave occupants' ensures strictly no active tenants
        $emptyRooms = Kamar::doesntHave('occupants')
            ->where('status', 'available')
            ->get();

        $limitDate = Carbon::today()->subDays(30);

        foreach ($emptyRooms as $room) {
            // Get last occupancy history
            $lastHistory = \App\Models\KamarOccupancyHistory::where('kamar_id', $room->id)
                ->whereNotNull('check_out_date')
                ->orderBy('check_out_date', 'desc')
                ->first();

            $shouldNotify = false;

            if ($lastHistory) {
                // Check if vacant for > 30 days
                if (Carbon::parse($lastHistory->check_out_date)->lt($limitDate)) {
                    $shouldNotify = true;
                }
            } else {
                // Never occupied. Check creation date.
                if ($room->created_at->lt($limitDate)) {
                    $shouldNotify = true;
                }
            }

            if ($shouldNotify) {
                // Rate limit: Check if we notified about this room in the last 30 days
                $alreadyNotified = Notification::where('type', 'info')
                    ->where('related_entity_type', 'room')
                    ->where('related_entity_id', $room->id)
                    ->where('title', 'Peluang Marketing')
                    ->where('created_at', '>', $limitDate) 
                    ->exists();

                if (!$alreadyNotified) {
                    Notification::create([
                        'user_id' => $owner->id,
                        'type' => 'info',
                        'category' => 'system',
                        'title' => 'Peluang Marketing',
                        'message' => "Kamar {$room->room_number} sudah kosong lebih dari 30 hari. Pertimbangkan untuk promosi ulang.",
                        'related_entity_type' => 'room',
                        'related_entity_id' => $room->id,
                        'priority' => 'low',
                        'action_required' => false,
                    ]);
                    
                     $this->info("Notified owner about empty room: {$room->room_number}");
                }
            }
        }
    }
}
