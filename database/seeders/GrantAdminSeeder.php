<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class GrantAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);

        $user = User::where('email', 'admin@example.test')->orWhere('id', 2)->first();

        if ($user) {
            $user->assignRole($role);
            echo "Assigned role 'admin' to user id {$user->id}\n";
        } else {
            echo "No user found to assign 'admin' role.\n";
        }
    }
}
