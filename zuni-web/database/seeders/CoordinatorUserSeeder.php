<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CoordinatorUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'coordinator01'],
            [
                'name' => 'Coordenador',
                
                'password' => Hash::make('123456'),
                'role' => 'coordinator',
            ]
        );
    }
}