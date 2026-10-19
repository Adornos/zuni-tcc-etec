<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\CoordinatorSheet;
use Database\Seeders\Concerns\SeedsPeople;
use Illuminate\Database\Seeder;

/** 1 coordenador. Login: coordinator01 (ou coordinator@zuni.test) / 123456 */
class CoordinatorSeeder extends Seeder
{
    use SeedsPeople;

    public function run(): void
    {
        $user = $this->upsertUser('coordinator01', [
            'name' => 'Márcia Regina Duarte',
            'email' => 'coordinator@zuni.test',
            'role' => UserRole::COORDINATOR,
            'status' => UserStatus::ACTIVE,

            'cpf' => $this->cpf(2),
            'rg' => $this->rg(2),
            'phone' => '(11) 97777-6666',
            'birth_date' => '1988-04-17',
            'gender' => 'F',

            'street' => 'Rua das Acácias',
            'number' => '245',
            'district' => 'Vila Mariana',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

        CoordinatorSheet::updateOrCreate(['coordinator_id' => $user->id], [
            'formation' => 'Licenciatura em Pedagogia',
            'specialization' => 'Gestão Escolar',
            'registration' => 'COORD001',
            'hire_date' => '2021-02-01',
            'notes' => 'Coordenadora responsável pelo acompanhamento pedagógico.',
        ]);
    }
}
