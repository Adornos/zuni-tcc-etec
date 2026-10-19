<?php

namespace App\Console\Commands;

use App\Services\Proficiency\ClassroomAverageService;
use App\Services\Proficiency\ProficiencyService;
use Illuminate\Console\Command;

class RefreshProficiencies extends Command
{
    protected $signature = 'proficiency:refresh';

    protected $description = 'Recalcula o valor em cache de todas as proficiências e as médias atuais das salas (use após migrar ou alterar parâmetros).';

    public function handle(ProficiencyService $proficiencies, ClassroomAverageService $averages): int
    {
        $refreshed = $proficiencies->refreshAll();
        $averages->refreshAll();

        $this->info("Valores atualizados: {$refreshed}. Médias das salas recalculadas.");

        return self::SUCCESS;
    }
}
