<?php

namespace KodePandai\Modular\Providers;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once __DIR__.'/../../src/helpers.php';
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \KodePandai\Modular\Commands\InstallCommand::class,
                \KodePandai\Modular\Commands\ModuleDeleteCommand::class,
                \KodePandai\Modular\Commands\ModuleListCommand::class,
                \KodePandai\Modular\Commands\ModuleMakeCommand::class,
            ]);
        }
    }
}
