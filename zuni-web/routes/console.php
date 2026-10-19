<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Snapshot diário das médias de proficiência das salas (requer scheduler: `php artisan schedule:work` ou cron).
Schedule::command('proficiency:snapshot')->dailyAt('23:55');
