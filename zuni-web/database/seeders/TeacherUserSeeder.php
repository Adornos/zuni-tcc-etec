<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'teacher01'],
            [
                'name' => 'Default Teacher',
                'password' => Hash::make('123456'),
                'role' => 'teacher',
            ]
        );
    }
}