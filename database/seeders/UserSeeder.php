<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['name' => 'admin'],
            [
                'email' => 'admin@maintainnow.id',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            ['name' => 'petugas'],
            [
                'email' => 'petugas@maintainnow.id',
                'role' => 'user',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
