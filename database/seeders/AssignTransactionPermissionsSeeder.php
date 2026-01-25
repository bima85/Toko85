<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignTransactionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure permissions exist
        $salePerm = Permission::firstOrCreate(['name' => 'transactions.view.sale']);
        $purchasePerm = Permission::firstOrCreate(['name' => 'transactions.view.purchase']);

        // Give both permissions to superadmin role if exists
        $super = Role::where('name', 'superadmin')->first();
        if ($super) {
            $super->givePermissionTo([$salePerm, $purchasePerm]);
        }

        // For the specific admingudang user: ensure only sale view is granted
        $user = User::where('email', 'admingudang@toko85.com')->first();
        if ($user) {
            // revoke purchase permission if present
            if ($user->hasPermissionTo($purchasePerm)) {
                $user->revokePermissionTo($purchasePerm);
            }

            // give sale permission explicitly
            if (! $user->hasPermissionTo($salePerm)) {
                $user->givePermissionTo($salePerm);
            }
        }
    }
}
