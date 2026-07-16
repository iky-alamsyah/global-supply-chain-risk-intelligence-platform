<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's admin user.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            [
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('admin');
    }
}