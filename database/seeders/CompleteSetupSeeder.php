<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompleteSetupSeeder extends Seeder
{
    /**
     * Run all essential seeders for a complete system setup.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Complete System Setup...');

        $this->command->info('📋 Step 1: Setting up Roles and Permissions...');
        $this->call([
            RoleSeeder::class,
            AddTransactionTabPermissionSeeder::class,
            AssignTransactionPermissionsSeeder::class,
        ]);

        $this->command->info('👤 Step 2: Creating Admin User...');
        $this->call([
            AdminUserSeeder::class,
        ]);

        $this->command->info('🏪 Step 3: Setting up Master Data (Stores, Warehouses, Units)...');
        $this->call([
            StoreWarehouseUnitSeeder::class,
        ]);

        $this->command->info('📦 Step 4: Adding Sample Products...');
        $this->call([
            BerasMENTIKSeeder::class,
            ShopCustomSeeder::class,
        ]);

        $this->command->info('✅ Complete System Setup finished successfully!');
        $this->command->info('');
        $this->command->info('🔑 Admin Login Credentials:');
        $this->command->info('   Email: admin@example.test');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('🎉 Your Shop85 system is ready to use!');
    }
}
