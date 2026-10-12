<?php

namespace App\Console\Commands;

use App\Services\Proficiency\ClassroomAverageService;
use App\Services\Proficiency\ProficiencyService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SnapshotProficiencies extends Command
{
    protected $signature = 'proficiency:snapshot {--date= : Data de referência (YYYY-MM-DD). Padrão: hoje}';

    protected $description = 'Atualiza o valor em cache dos alunos (decaimento do tempo), as médias das salas e grava o snapshot do dia (idempotente).';

    public function handle(ProficiencyService $proficiencies, ClassroomAverageService $averages): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();

        // O cache sempre reflete "agora"; o snapshot de uma data usa as observações até o fim dela.
        $refreshed = $proficiencies->refreshAll();
        $created = $averages->snapshot($date);

        $this->info("Valores atualizados: {$refreshed}. Snapshots de {$date->toDateString()}: {$created} novo(s).");

        return self::SUCCESS;
    }
}
