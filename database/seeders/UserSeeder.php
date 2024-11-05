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
        $roles = ['admin', 'manager', 'cs', 'staff', 'customer'];

        for ($i = 0; $i < count($roles); $i++) {
            User::create([
                'name' => $roles[$i] . ' Tokoponik',
                'username' => $roles[$i] . 'tokoponik',
                'password' => $roles[$i] . '123',
                'role' => $roles[$i],
                'phone_number' => '081234567890'
            ]);
        }
    }
}
