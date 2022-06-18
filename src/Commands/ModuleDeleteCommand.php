<?php

namespace KodePandai\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ModuleDeleteCommand extends Command
{
    protected $signature = 'modular:module:delete {name}';

    protected $description = 'Delete existing module';

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

        if ($this->ask("Are you sure you want to delete module $name? (Y/n)") != 'Y') {
            return self::FAILURE;
        }

        $this->line("> Running composer remove modules/{$nameSlug}..");

        $installProcess = new Process(['composer', 'remove', "modules/{$nameSlug}"], null);
        $installProcess->setWorkingDirectory(base_path());
        $installProcess->mustRun(function ($type, $buffer) {
            $this->getOutput()->write($buffer);
        });
        $installProcess->wait();

        $this->line("> Removing modules/{$nameSlug} folder..");

        File::deleteDirectory(module_path($name));

        $this->line("> Success!");

        return self::SUCCESS;
    }
}
