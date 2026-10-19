<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\DirectorSheet;
use Database\Seeders\Concerns\SeedsPeople;
use Illuminate\Database\Seeder;

/** 1 diretor. Login: director01 (ou director@zuni.test) / 123456 */
class DirectorSeeder extends Seeder
{
    use SeedsPeople;

    public function run(): void
    {
        $user = $this->upsertUser('director01', [
            'name' => 'Roberto Carlos Menezes',
            'email' => 'director@zuni.test',
            'role' => UserRole::DIRECTOR,
            'status' => UserStatus::ACTIVE,

            'cpf' => $this->cpf(1),
            'rg' => $this->rg(1),
            'phone' => '(11) 96666-5555',
            'birth_date' => '1980-03-15',
            'gender' => 'M',

            'street' => 'Avenida Central',
            'number' => '500',
            'district' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);

        DirectorSheet::updateOrCreate(['director_id' => $user->id], [
            'formation' => 'Licenciatura em Pedagogia',
            'specialization' => 'Gestão e Administração Escolar',
            'registration' => 'DIR001',
            'hire_date' => '2018-02-01',
            'notes' => 'Diretor da escola.',
        ]);
    }
}
