<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AllSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Running AllSeeders...');

        $seeders = [
            RoleSeeder::class,
            AdminUserSeeder::class,
            GrantAdminSeeder::class,
            StoreWarehouseUnitSeeder::class,
            ShopCustomSeeder::class,
            BerasMENTIKSeeder::class,
            BerasC4Seeder::class,
            BerasKETANSeeder::class,
            BimsSelectiveSeeder::class,
            Bims2916Toko85IgnoreSeeder::class,
            Bims2916Toko85Seeder::class,
            StockBatchSeeder::class,
            StockCardSeeder::class,
            TransactionHistorySeeder::class,
        ];

        foreach ($seeders as $seeder) {
            $this->call($seeder);
        }
    }
}
