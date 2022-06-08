<?php

namespace KodePandai\Modular\Commands;

use Illuminate\Console\Command;

class ModularInstallCommand extends Command
{
    protected $signature = 'modular:install';

    protected $description = 'Instalasi laravel modular.';

    public function handle(): int
    {
        $this->line("\n buat folder modules..");

        if (! file_exists(base_path('modules'))) {
            mkdir(base_path('modules'));
        }

        $this->line('> salin konfigurasi modular..');

        if (! file_exists(config_path('modular.php'))) {
            copy(__DIR__.'/../../config/modular.php', config_path('modular.php'));
        }

        $this->info('Sukses!');

        return self::SUCCESS;
    }
}
