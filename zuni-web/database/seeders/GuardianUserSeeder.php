<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuardianUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'guardian01'],
            [
                'name' => 'Default Guardian',
                'password' => Hash::make('123456'),
                'role' => 'guardian',
            ]
        );
    }
}