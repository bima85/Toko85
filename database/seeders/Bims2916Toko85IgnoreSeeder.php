<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Bims2916Toko85IgnoreSeeder extends Seeder
{
    /**
     * Run the database seeds using INSERT IGNORE and skipping problematic tables.
     */
    public function run(): void
    {
        $path = base_path('bims2916_toko85.sql');

        if (!File::exists($path)) {
            $this->command->error("SQL file not found: {$path}");
            return;
        }

        $sql = File::get($path);

        preg_match_all('/INSERT\s+INTO\s+`([^`]+)`.*?;\s*/is', $sql, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            $this->command->warn('No INSERT statements found in SQL file.');
            return;
        }

        $skip = array_map('trim', explode(',', env('BIMS_SKIP_TABLES', 'sessions,migrations')));
        $skip = array_filter($skip);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($matches as $m) {
            $table = $m[1];

            if (in_array($table, $skip, true)) {
                $this->command->warn("Skipping table '{$table}' by configuration.");
                continue;
            }

            if (!Schema::hasTable($table)) {
                $this->command->info("Skipping table '{$table}' because it does not exist in the current DB.");
                continue;
            }

            $insertSql = $m[0];
            // Use INSERT IGNORE to avoid duplicate PK errors
            $insertSql = preg_replace('/^INSERT\s+INTO/i', 'INSERT IGNORE INTO', $insertSql);

            try {
                DB::unprepared($insertSql);
                $this->command->info("Imported (ignore) into '{$table}'.");
            } catch (\Throwable $e) {
                $this->command->error("Failed to import into '{$table}': " . $e->getMessage());
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
