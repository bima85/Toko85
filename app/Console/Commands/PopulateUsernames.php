<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PopulateUsernames extends Command
{
    protected $signature = 'users:populate-usernames {--dry-run}';
    protected $description = 'Populate missing usernames for users who do not have one yet (ensures uniqueness)';

    public function handle()
    {
        $users = User::whereNull('username')->orWhere('username', '')->get();
        if ($users->isEmpty()) {
            $this->info('No users without username found.');
            return 0;
        }

        foreach ($users as $user) {
            $base = $user->name ? Str::slug($user->name, '') : explode('@', $user->email)[0];
            $username = $base;
            $i = 1;
            while (User::where('username', $username)->exists()) {
                $username = $base . $i;
                $i++;
            }

            $this->line("User {$user->email} -> username: $username");
            if (!$this->option('dry-run')) {
                $user->username = $username;
                $user->save();
            }
        }

        $this->info('Done.');
        return 0;
    }
}
