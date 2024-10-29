<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Tokoponik',
            'username' => 'admintokoponik',
            'password' => 'admin123',
            'role' => 'admin',
            'phone_number' => '081234567890'
        ]);
    }
}
