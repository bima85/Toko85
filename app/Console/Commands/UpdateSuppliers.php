<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSuppliers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:suppliers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup suppliers table and update nama_supplier from owner where available';

    public function handle()
    {
        $this->info('Creating backup table suppliers_backup (if not exists)');
        DB::statement('CREATE TABLE IF NOT EXISTS suppliers_backup LIKE suppliers');
        DB::statement('TRUNCATE TABLE suppliers_backup');
        DB::statement('INSERT INTO suppliers_backup SELECT * FROM suppliers');
        $this->info('Backup completed.');

        $this->info('Updating nama_supplier from owner where owner is present...');
        $affected = DB::update("UPDATE suppliers SET nama_supplier = owner WHERE owner IS NOT NULL AND TRIM(owner) <> ''");
        $this->info('Updated rows: ' . $affected);

        $count = DB::table('suppliers')
            ->whereNotNull('owner')
            ->whereRaw('TRIM(owner) <> ""')
            ->whereColumn('nama_supplier', 'owner')
            ->count();

        $this->info('Rows now matching nama_supplier == owner: ' . $count);

        return 0;
    }
}
