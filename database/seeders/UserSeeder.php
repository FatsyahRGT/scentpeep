<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@scentpeep.com'],
            [
                'name' => 'Admin Scentpeep',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@scentpeep.com'],
            [
                'name' => 'Staff Scentpeep',
                'role' => 'staff',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
    }
}
