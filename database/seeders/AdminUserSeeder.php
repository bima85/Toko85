<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      $email = 'admin@example.test';

      $user = User::firstOrCreate(
         ['email' => $email],
         [
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => 'password', // will be hashed by model cast
         ]
      );

      // if username column exists but user was created earlier without username, ensure it's set
      if (empty($user->username) && \Illuminate\Support\Facades\Schema::hasColumn('users', 'username')) {
         $user->username = 'admin';
         $user->save();
      }

      // Ensure role exists then assign
      if (method_exists($user, 'assignRole')) {
         $user->assignRole('admin');
      }

      $this->command->info("Admin user seeded: {$email} (password: password)");
   }
}
