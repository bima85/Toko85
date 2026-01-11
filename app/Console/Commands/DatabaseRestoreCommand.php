<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class DatabaseRestoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore
                            {--file= : Path to SQL backup file (default: bims2916_toko85.sql)}
                            {--fresh : Run migrate:fresh before restore}
                            {--force : Skip all confirmations (use with caution)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from SQL backup file with safety confirmations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file') ?: 'bims2916_toko85.sql';
        $path = base_path($file);
        $isFresh = $this->option('fresh');
        $force = $this->option('force');

        $this->alert('🚨 DATABASE RESTORE COMMAND 🚨');
        $this->warn('This command can potentially overwrite or delete existing data!');
        $this->newLine();

        // Check if file exists
        if (!File::exists($path)) {
            $this->error("❌ Backup file not found: {$path}");
            return Command::FAILURE;
        }

        $this->info("📁 Backup file found: {$path}");
        $this->info('📊 File size: ' . $this->formatBytes(File::size($path)));
        $this->newLine();

        // Show current database status
        $this->showCurrentStatus();

        // Multiple confirmations if not forced
        if (!$force) {
            if (!$this->confirmMultiple()) {
                $this->info('✅ Operation cancelled by user.');
                return Command::SUCCESS;
            }
        } else {
            $this->warn('⚠️  FORCE MODE ENABLED - Skipping confirmations!');
        }

        // Execute restore
        try {
            $this->performRestore($path, $isFresh);
            $this->newLine();
            $this->info('✅ Database restore completed successfully!');
            $this->showFinalStatus();
        } catch (\Exception $e) {
            $this->error('❌ Restore failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function confirmMultiple(): bool
    {
        $this->newLine();
        $this->warn('🔒 SAFETY CONFIRMATIONS:');

        // Confirmation 1: General warning
        if (!$this->confirm('Are you sure you want to restore the database from backup?', false)) {
            return false;
        }

        // Confirmation 2: Type the word "RESTORE"
        $confirmation = $this->ask('Type "RESTORE" to confirm this destructive operation');
        if (strtoupper($confirmation) !== 'RESTORE') {
            $this->error('❌ Incorrect confirmation word. Operation cancelled.');
            return false;
        }

        // Confirmation 3: Final warning
        if (!$this->confirm('⚠️  FINAL WARNING: This will modify your database. Continue?', false)) {
            return false;
        }

        return true;
    }

    private function showCurrentStatus()
    {
        $this->info('📈 Current Database Status:');

        $tables = [
            'users' => 'Users',
            'products' => 'Products',
            'categories' => 'Categories',
            'sales' => 'Sales',
            'purchases' => 'Purchases',
            'stock_adjustments' => 'Stock Adjustments',
            'stock_batches' => 'Stock Batches',
            'stock_cards' => 'Stock Cards',
        ];

        foreach ($tables as $table => $label) {
            try {
                $count = DB::table($table)->count();
                $this->line("  {$label}: {$count} records");
            } catch (\Exception $e) {
                $this->line("  {$label}: Table not accessible");
            }
        }
        $this->newLine();
    }

    private function showFinalStatus()
    {
        $this->info('📊 Final Database Status:');
        $this->showCurrentStatus();
    }

    private function performRestore(string $path, bool $isFresh)
    {
        // Run migrate:fresh if requested
        if ($isFresh) {
            $this->info('🔄 Running migrate:fresh...');
            Artisan::call('migrate:fresh', ['--force' => true], $this->getOutput());
            $this->info('✅ Migration completed.');
        }

        // Read and parse SQL file
        $this->info('📖 Reading backup file...');
        $sql = File::get($path);

        // Extract INSERT statements
        preg_match_all('/INSERT\s+INTO\s+`([^`]+)`.*?;\s*/is', $sql, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            $this->warn('⚠️  No INSERT statements found in backup file.');
            return;
        }

        $this->info("📝 Found " . count($matches) . " tables to restore.");
        $this->newLine();

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $progressBar = $this->output->createProgressBar(count($matches));
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($matches as $m) {
            $table = $m[1];
            $insertSql = $m[0];

            if (!Schema::hasTable($table)) {
                $this->newLine();
                $this->warn("⚠️  Skipping table '{$table}' - does not exist in current schema.");
                $progressBar->advance();
                continue;
            }

            try {
                DB::unprepared($insertSql);
                $successCount++;
            } catch (\Throwable $e) {
                $errorCount++;
                $this->newLine();
                $this->error("❌ Failed to import '{$table}': " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("✅ Successfully imported: {$successCount} tables");
        if ($errorCount > 0) {
            $this->warn("⚠️  Failed to import: {$errorCount} tables");
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }
}
