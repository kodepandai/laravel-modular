<?php

namespace KodePandai\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'modular:install {--force}';

    protected $description = 'Install laravel modular in your laravel app';

    public function handle(): int
    {
        $this->line("> Creating modules folder..");

        $dirExists = File::exists(base_path('modules'));

        if ($dirExists && ! $this->hasOption('force')) {
            //.
            $this->error("Modules folder already exists. If you want to ovverride it, please use with --force.");

            return self::FAILURE;
        }

        $question = "Are you sure you want to delete all content in the modules folder? (Y/n)";

        if ($dirExists && $this->ask($question) != 'Y') {
            return self::FAILURE;
        }

        if ($dirExists) {
            File::deleteDirectory(base_path('modules'));
            File::makeDirectory((base_path('modules')));
            File::put(base_path('modules/.gitkeep'), '');
        }

        $this->info("> Success!");

        return self::SUCCESS;
    }
}
