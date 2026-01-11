<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BimsSelectiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Set `BIMS_IMPORT_TABLES` in your .env to a comma-separated list of table names,
     * e.g. BIMS_IMPORT_TABLES=products,categories
     */
    public function run(): void
    {
        $path = base_path('bims2916_toko85.sql');

        if (!File::exists($path)) {
            $this->command->error("SQL file not found: {$path}");
            return;
        }

        $tablesEnv = env('BIMS_IMPORT_TABLES', 'products');
        $requested = array_map('strtolower', array_map('trim', explode(',', $tablesEnv)));

        $sql = File::get($path);

        preg_match_all('/INSERT\s+INTO\s+`([^`]+)`.*?;\s*/is', $sql, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            $this->command->warn('No INSERT statements found in SQL file.');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($matches as $m) {
            $table = $m[1];
            if (!in_array(strtolower($table), $requested, true)) {
                continue;
            }

            if (!Schema::hasTable($table)) {
                $this->command->warn("Table '{$table}' does not exist in current DB — skipped.");
                continue;
            }

            $insertSql = $m[0];
            try {
                DB::unprepared($insertSql);
                $this->command->info("Imported data into '{$table}'.");
            } catch (\Throwable $e) {
                $this->command->error("Failed to import into '{$table}': " . $e->getMessage());
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
