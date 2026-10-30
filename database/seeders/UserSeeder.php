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

        $team = [
            ['email' => 'rizky@scentpeep.com', 'name' => 'RIZKY', 'role' => 'staff'],
            ['email' => 'iwan@scentpeep.com', 'name' => 'IWAN', 'role' => 'staff'],
            ['email' => 'vanny@scentpeep.com', 'name' => 'VANNY', 'role' => 'staff'],
        ];

        foreach ($team as $member) {
            User::updateOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'role' => $member['role'],
                    'is_active' => true,
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
