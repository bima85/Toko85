<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the role and permission seeders.
     */
    public function run(): void
    {
        $this->command->info('Running Role and Permission Seeders...');

        $this->call([
            RoleSeeder::class,
            AddTransactionTabPermissionSeeder::class,
            AssignTransactionPermissionsSeeder::class,
        ]);

        $this->command->info('Role and Permission seeders completed successfully!');
    }
}
