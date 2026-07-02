<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * One-shot migration to move payment proofs and tenant identity documents
 * from the public disk to the private 'local' disk.
 *
 * Earlier code stored these on the public disk, exposing them at
 * /storage/payment-proofs/... and /storage/tenant-documents/... — anyone
 * with the path could view them. Newer uploads go straight to the private
 * disk and the BuktiBayar::proof_url accessor / ProfilController::viewDocument
 * route already fall back to either disk, so moving the files is enough to
 * close the leak without any DB changes.
 *
 * Run before deploying to production. Idempotent: skips files that already
 * exist at the destination.
 */
class MigrateSensitiveFilesToPrivate extends Command
{
    protected $signature = 'storage:migrate-private
                            {--dry-run : Show what would be moved without actually moving files}';

    protected $description = 'Move legacy payment proofs and tenant documents from public to private storage.';

    private const PATHS = ['payment-proofs', 'tenant-documents'];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $public = Storage::disk('public');
        $private = Storage::disk('local');

        $totalMoved = 0;
        $totalSkipped = 0;
        $totalFailed = 0;

        foreach (self::PATHS as $base) {
            if (!$public->exists($base)) {
                $this->info("[$base] No legacy files on public disk — nothing to move.");
                continue;
            }

            $files = $public->allFiles($base);
            $this->info("[$base] " . count($files) . ' legacy file(s) found on public disk.');

            foreach ($files as $file) {
                if ($private->exists($file)) {
                    $this->line("  - skip (exists on private): {$file}");
                    $totalSkipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->line("  - would move: {$file}");
                    $totalMoved++;
                    continue;
                }

                try {
                    $private->writeStream($file, $public->readStream($file));
                    $public->delete($file);
                    $totalMoved++;
                    $this->line("  - moved: {$file}");
                } catch (\Throwable $e) {
                    $totalFailed++;
                    $this->error("  - FAILED ({$file}): {$e->getMessage()}");
                }
            }

            // Remove empty directories left behind on the public disk.
            if (!$dryRun) {
                foreach (array_reverse($public->allDirectories($base)) as $dir) {
                    if (empty($public->allFiles($dir)) && empty($public->allDirectories($dir))) {
                        $public->deleteDirectory($dir);
                    }
                }
                // Finally remove the top-level dir if empty.
                if (empty($public->allFiles($base)) && empty($public->allDirectories($base))) {
                    $public->deleteDirectory($base);
                }
            }
        }

        $this->newLine();
        $this->info("Done. Moved: {$totalMoved}, Skipped: {$totalSkipped}, Failed: {$totalFailed}" . ($dryRun ? ' (dry run)' : ''));

        return $totalFailed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
