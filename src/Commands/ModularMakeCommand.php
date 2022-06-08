<?php

namespace KodePandai\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class ModularMakeCommand extends Command
{
    protected $signature = 'modular:make {name}';

    protected $description = 'Buat modul baru.';

    public function __construct(public Composer $composer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $name = ucfirst($this->argument('name'));

        if (file_exists(module_path($name))) {
            $this->warn("Modul {$name} sudah ada!");

            return self::FAILURE;
        }

        $this->line("\nIni akan membuat 2 file:");
        $this->line("- modules/{$name}/composer.json");
        $this->line("- modules/{$name}/src/{$name}ServiceProvider.php");

        $answer = $this->ask("Lanjutkan membuat modul {$name} ? (Y/n)", 'n');

        if ($answer != 'Y') {
            $this->warn('Dibatalkan!');

            return self::FAILURE;
        }

        $this->line('> Buat folder..');

        mkdir(module_path($name));
        mkdir(module_path("{$name}/src"));

        $this->line('> Buat composer.json..');

        $composerContent = file_get_contents(__DIR__.'/../../stubs/composer.json.stub');
        $composerContent = preg_replace('/MODULENAME/', $name, $composerContent);

        file_put_contents(module_path("{$name}/composer.json"), $composerContent);

        $this->line('> Buat service provider..');

        $providerContent = file_get_contents(__DIR__.'/../../stubs/ModuleNameServiceProvider.php.stub');
        $providerContent = preg_replace('/MODULENAME/', $name, $providerContent);

        file_put_contents(module_path("{$name}/src/{$name}ServiceProvider.php"), $providerContent);

        $this->line('> Perbarui config modular..');

        $configContent = file_get_contents(config_path('modular.php'));
        $notLoaded = preg_grep("/{$name}ServiceProvider/", [$configContent]) == [];

        if ($notLoaded) {
            // kekurangan: ini akan meletakkan modul baru di tengah-tengah
            $configContent = preg_replace(
                "/(ServiceProvider::class,[\s\S]*?])/",
                "ServiceProvider::class,\n        '$name' => $name\\$name$1",
                $configContent
            );
        }

        file_put_contents(config_path('modular.php'), $configContent);

        $this->line('> Jalankan composer dump-autoload');

        $this->composer->dumpAutoloads();
        $this->composer->dumpOptimized();

        $this->line('Sukses!');

        return self::SUCCESS;
    }
}
