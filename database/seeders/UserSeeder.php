<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Master Admin', 'email' => 'master@it-monitoring.test', 'role' => User::ROLE_MASTER],
            ['name' => 'Admin IT', 'email' => 'adminit@it-monitoring.test', 'role' => User::ROLE_ADMIN_IT],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user + ['password' => 'password123', 'is_active' => true]
            );
        }
    }
}
