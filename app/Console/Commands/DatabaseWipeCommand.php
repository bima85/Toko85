<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseWipeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:wipe-safe
                            {--force : Skip confirmations (use with extreme caution)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely wipe all data from database with multiple confirmations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        $this->alert('🚨 DANGER: DATABASE WIPE COMMAND 🚨');
        $this->error('This command will DELETE ALL DATA from your database!');
        $this->error('This action CANNOT be undone!');
        $this->newLine();

        // Show current status
        $this->showCurrentStatus();

        // Multiple confirmations if not forced
        if (!$force) {
            if (!$this->confirmWipe()) {
                $this->info('✅ Database wipe cancelled by user.');
                return Command::SUCCESS;
            }
        } else {
            $this->error('💀 FORCE MODE ENABLED - All data will be destroyed!');
            $this->newLine();
        }

        // Execute wipe
        try {
            $this->performWipe();
            $this->newLine();
            $this->info('✅ Database wipe completed successfully!');
            $this->showCurrentStatus();
        } catch (\Exception $e) {
            $this->error('❌ Wipe failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function confirmWipe(): bool
    {
        $this->newLine();
        $this->warn('🔒 MULTIPLE SAFETY CONFIRMATIONS REQUIRED:');

        // Confirmation 1: Basic confirmation
        if (!$this->confirm('Do you really want to wipe ALL data from the database?', false)) {
            return false;
        }

        // Confirmation 2: Type database name
        $dbName = env('DB_DATABASE', 'unknown');
        $typedName = $this->ask("Type the database name '{$dbName}' to confirm");
        if ($typedName !== $dbName) {
            $this->error('❌ Incorrect database name. Operation cancelled.');
            return false;
        }

        // Confirmation 3: Type "WIPE ALL DATA"
        $confirmation = $this->ask('Type "WIPE ALL DATA" in uppercase to confirm total destruction');
        if ($confirmation !== 'WIPE ALL DATA') {
            $this->error('❌ Incorrect confirmation phrase. Operation cancelled.');
            return false;
        }

        // Confirmation 4: Final warning with data summary
        $totalRecords = $this->getTotalRecordCount();
        $this->warn("⚠️  FINAL WARNING: You are about to delete {$totalRecords} records!");
        if (!$this->confirm('Are you absolutely sure? This cannot be undone!', false)) {
            return false;
        }

        return true;
    }

    private function showCurrentStatus()
    {
        $this->info('📊 Current Database Status:');

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

        $totalRecords = 0;
        foreach ($tables as $table => $label) {
            try {
                $count = DB::table($table)->count();
                $totalRecords += $count;
                $this->line("  {$label}: {$count} records");
            } catch (\Exception $e) {
                $this->line("  {$label}: Table not accessible");
            }
        }

        $this->info("📈 Total records: {$totalRecords}");
        $this->newLine();
    }

    private function getTotalRecordCount(): int
    {
        $tables = [
            'users',
            'products',
            'categories',
            'sales',
            'purchases',
            'stock_adjustments',
            'stock_batches',
            'stock_cards',
            'purchase_items',
            'sale_items',
            'suppliers',
            'customers'
        ];

        $total = 0;
        foreach ($tables as $table) {
            try {
                $total += DB::table($table)->count();
            } catch (\Exception $e) {
                // Skip inaccessible tables
            }
        }

        return $total;
    }

    private function performWipe()
    {
        $this->info('🗑️  Starting database wipe...');

        // Tables to wipe (in order to respect foreign keys)
        $tables = [
            'stock_cards',
            'stock_batches',
            'sale_items',
            'sales',
            'purchase_items',
            'purchases',
            'stock_adjustments',
            'transaction_histories',
            'products',
            'subcategories',
            'categories',
            'suppliers',
            'customers',
            'warehouses',
            'stores',
            'units',
            'model_has_roles',
            'role_has_permissions',
            'roles',
            'permissions',
            'sessions',
            'cache',
            'jobs',
            'failed_jobs',
        ];

        $progressBar = $this->output->createProgressBar(count($tables));
        $progressBar->start();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->newLine();
                    $this->info("🗑️  Wiped table: {$table}");
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("⚠️  Could not wipe {$table}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $progressBar->finish();
        $this->newLine();

        // Keep admin user and basic roles
        $this->info('👤 Keeping essential user and role data...');
    }
}
