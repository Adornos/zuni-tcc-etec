<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\StudentSheet;
use App\Services\Proficiency\ClassroomAverageService;
use App\Services\Proficiency\ProficiencyService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Inicializa as proficiências (valor neutro) dos alunos ativos e calcula as médias das turmas.
 * As habilidades em si são cadastradas pela migration das proficiências.
 */
class ProficiencySeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('proficiencies')) {
            $this->command?->warn('Tabelas de proficiências não encontradas: ProficiencySeeder ignorado.');

            return;
        }

        $service = app(ProficiencyService::class);

        StudentSheet::query()
            ->whereHas('user', fn ($q) => $q->where('status', UserStatus::ACTIVE->value))
            ->each(fn (StudentSheet $sheet) => $service->ensureInitialized($sheet));

        app(ClassroomAverageService::class)->refreshAll();
    }
}
