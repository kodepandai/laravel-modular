<?php

namespace KodePandai\Modular;

use Illuminate\Support\ServiceProvider;

class ModularServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once __DIR__.'/../src/helpers.php';

        $this->mergeConfigFrom(__DIR__.'/../config/modular.php', 'modular');

        foreach (config('modular.modules') as $name => $provider) {
            $this->app->register($provider);
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \KodePandai\Modular\Commands\ModularInstallCommand::class,
                \KodePandai\Modular\Commands\ModularListCommand::class,
                \KodePandai\Modular\Commands\ModularMakeCommand::class,
            ]);
        }
    }
}
