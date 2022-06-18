<?php

namespace KodePandai\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ModuleMakeCommand extends Command
{
    protected $signature = 'modular:module:make {name}';

    protected $description = 'Create new module';

    protected Composer $composer;

    public function __construct(Composer $composer)
    {
        $this->composer = $composer;

        parent::__construct();
    }

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $nameSlug = Str::slug($this->argument('name'));

        if ($this->ask("Are you sure you want to create module $name? (Y/n)") != 'Y') {
            return self::FAILURE;
        }

        if (File::exists(module_path($name))) {
            //.
            $this->error("Module {$name} already exists!");

            return self::FAILURE;
        }

        $this->line("> Creating composer.json file..");

        File::ensureDirectoryExists(module_path($name));
        File::makeDirectory(module_path("{$name}/src"));
        File::makeDirectory(module_path("{$name}/src/Providers"));

        $composerContent = File::get(__DIR__.'/../../stubs/composer.json.stub');
        $composerContent = preg_replace('/_MODULENAME_/', $name, $composerContent);
        $composerContent = preg_replace('/_MODULENAMESLUG_/', $nameSlug, $composerContent);

        File::put(module_path("{$name}/composer.json"), $composerContent);

        $this->line("> Creating {$name}ServiceProvider file..");

        $providerContent = File::get(__DIR__.'/../../stubs/ModuleNameServiceProvider.php.stub');
        $providerContent = preg_replace('/_MODULENAME_/', $name, $providerContent);

        File::put(module_path("{$name}/src/Providers/{$name}ServiceProvider.php"), $providerContent);

        $this->line("> Running composer install modules/{$nameSlug}..");

        $installProcess = new Process(['composer', 'require', "modules/{$nameSlug}"], null);
        $installProcess->setWorkingDirectory(base_path());
        $installProcess->mustRun(function ($type, $buffer) {
            $this->getOutput()->write($buffer);
        });
        $installProcess->wait();

        $this->line("> Success!");

        return self::SUCCESS;
    }
}
