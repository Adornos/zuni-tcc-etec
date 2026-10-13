<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Dados de desenvolvimento (idempotente: rodar de novo atualiza os mesmos registros).
 *
 *   1 coordenador · 1 diretor · 5 professores · 5 responsáveis · 25 alunos (5 por responsável)
 *   + 5 turmas (1º ao 5º ano), uma por professor
 *
 * Todos os usuários usam a senha 123456. Recomendado: php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DirectorSeeder::class,
            CoordinatorSeeder::class,
            TeacherSeeder::class,
            ClassroomSeeder::class,
            GuardianSeeder::class,
            StudentSeeder::class,
            ProficiencySeeder::class,
        ]);
    }
}
