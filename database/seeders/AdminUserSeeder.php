<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/AdminUserSeeder.php
public function run()
{
    User::create([
        'first_name' => 'Admin',
        'last_name' => 'System',
        'phone' => '1234567890',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'status' => 'approved',
        'birth_date' => '1990-01-01'
    ]);
}
}
