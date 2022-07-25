<?php

namespace KodePandai\Modular\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModuleListCommand extends Command
{
    protected $signature = 'modular:module:list';

    protected $description = 'Get all modules';

    public function handle(): int
    {
        $rows = [];

        $packages = InstalledVersions::getInstalledPackages();
        $modules = preg_grep('/(modules\/(.*))/', $packages);

        foreach ($modules as $module) {
            // TODO: get whats loaded in the module (routes, config, etc)
            $name = Str::studly(str_replace('modules/', '', $module));
            $folder = 'modules'.DIRECTORY_SEPARATOR.$name;
            $rows[] = [$name, $folder];
        }

        $this->table(['Name', 'Folder'], $rows);

        return self::SUCCESS;
    }
}
