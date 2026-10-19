<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // bind (não singleton): lê config('proficiency') a cada resolução, o que
        // permite alterar parâmetros em testes sem reiniciar o container.
        $this->app->bind(
            \App\Services\Proficiency\ProficiencyCalculator::class,
            fn () => \App\Services\Proficiency\ProficiencyCalculator::fromConfig()
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}
