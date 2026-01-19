<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AssignSuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@example.test';

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->command->info("Admin user not found: {$email}");
            return;
        }

        $role = Role::where('name', 'superadmin')->first();
        if (! $role) {
            $this->command->info('Role superadmin not found');
            return;
        }

        // Ensure mapping exists in model_has_roles
        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ], []);

        $this->command->info("Assigned role 'superadmin' to {$email}");
    }
}
