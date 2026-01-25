<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddTransactionTabPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add explicit permission entries so they appear in the permission matrix UI
        Permission::firstOrCreate(['name' => 'transactions.view.purchase']);
        Permission::firstOrCreate(['name' => 'transactions.view.sale']);
        Permission::firstOrCreate(['name' => 'transactions.manage']);
    }
}
