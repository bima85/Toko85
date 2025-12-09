<?php

namespace Database\Seeders;

use App\Models\TransactionHistory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TransactionHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $transactionTypes = ['penjualan', 'pembelian', 'adjustment', 'return', 'other'];
        $statuses = ['completed', 'pending', 'failed', 'cancelled'];
        $paymentMethods = ['Cash', 'Transfer', 'Cek', 'Kartu Kredit', 'Lainnya'];

        for ($i = 0; $i < 100; $i++) {
            TransactionHistory::create([
                'transaction_code' => 'TRX-' . strtoupper($faker->unique()->bothify('??###??')) . '-' . date('Y'),
                'transaction_type' => $faker->randomElement($transactionTypes),
                'reference_id' => $faker->numberBetween(1, 50),
                'reference_type' => $faker->randomElement(['Sale', 'Purchase', 'StockAdjustment', 'Return']),
                'transaction_date' => $faker->dateTimeBetween('-3 months', 'now'),
                'amount' => $faker->numberBetween(100000, 50000000),
                'currency' => 'IDR',
                'description' => $faker->sentence(),
                'status' => $faker->randomElement($statuses),
                'user_id' => $faker->randomElement($users)->id,
                'payment_method' => $faker->randomElement($paymentMethods),
                'notes' => $faker->paragraph(),
                'metadata' => json_encode([
                    'ip_address' => $faker->ipv4(),
                    'user_agent' => $faker->userAgent(),
                    'batch_id' => $faker->numberBetween(1, 10),
                ]),
            ]);
        }
    }
}
