<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Bims2916Toko85Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('bims2916_toko85.sql');

        if (!File::exists($path)) {
            $this->command->error("SQL file not found: {$path}");
            return;
        }

        // Konfirmasi sebelum import data
        if (!$this->command->confirm('⚠️  PERHATIAN: Operasi ini akan mengimport data dari backup SQL dan dapat menimpa data yang ada. Apakah Anda yakin ingin melanjutkan?', false)) {
            $this->command->info('Operasi dibatalkan oleh user.');
            return;
        }

        $sql = File::get($path);

        // Extract all INSERT INTO `table` ...; statements (multiline aware)
        preg_match_all('/INSERT\s+INTO\s+`([^`]+)`.*?;\s*/is', $sql, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            $this->command->warn('No INSERT statements found in SQL file.');
            return;
        }

        $this->command->info('Memulai import data dari backup...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($matches as $m) {
            $table = $m[1];
            $insertSql = $m[0];

            if (!Schema::hasTable($table)) {
                $this->command->info("Skipping table '{$table}' because it does not exist in the current DB.");
                continue;
            }

            try {
                DB::unprepared($insertSql);
                $this->command->info("✓ Imported data into '{$table}'.");
            } catch (\Throwable $e) {
                $this->command->error("✗ Failed to import into '{$table}': " . $e->getMessage());
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('✅ Import data selesai!');
    }
}
