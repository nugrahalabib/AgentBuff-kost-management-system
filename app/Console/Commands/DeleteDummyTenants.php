<?php

namespace App\Console\Commands;

use App\Models\Kamar;
use App\Models\Transaksi;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Console\Command;

class DeleteDummyTenants extends Command
{
    protected $signature = 'cleanup:dummy-tenants {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Delete dummy tenants (Nunggak 1-3, Kontrak 1-2) and their related data';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $dummyNames = ['Nunggak 1', 'Nunggak 2', 'Nunggak 3', 'Kontrak 1', 'Kontrak 2'];
        
        $dummyTenants = User::where('role', 'tenant')
            ->whereIn('name', $dummyNames)
            ->get();
        
        if ($dummyTenants->isEmpty()) {
            $this->info('No dummy tenants found to delete.');
            return 0;
        }
        
        $this->info("Found {$dummyTenants->count()} dummy tenants to delete:");
        foreach ($dummyTenants as $tenant) {
            $this->line("  - {$tenant->name} (ID: {$tenant->id})");
        }
        
        $tenantIds = $dummyTenants->pluck('id')->toArray();
        
        if ($isDryRun) {
            $this->newLine();
            $this->comment('Dry run - no changes made.');
            return 0;
        }
        
        // Step 1: Clear room references
        $roomsUpdated = Kamar::whereIn('current_tenant_id', $tenantIds)
            ->update(['current_tenant_id' => null, 'status' => 'available']);
        $this->info("Cleared {$roomsUpdated} room references.");
        
        // Step 2: Delete transactions
        $transactionsDeleted = Transaksi::whereIn('penyewa_id', $tenantIds)->delete();
        $this->info("Deleted {$transactionsDeleted} transactions.");
        
        // Step 3: Delete tenant profiles
        $profilesDeleted = Penyewa::whereIn('user_id', $tenantIds)->delete();
        $this->info("Deleted {$profilesDeleted} tenant profiles.");
        
        // Step 4: Delete users
        $usersDeleted = User::whereIn('id', $tenantIds)->delete();
        $this->info("Deleted {$usersDeleted} dummy tenant accounts.");
        
        $this->newLine();
        $this->info('✅ Dummy tenants successfully deleted!');
        
        return 0;
    }
}
