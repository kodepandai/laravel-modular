<?php

namespace KodePandai\Modular\Commands;

use Illuminate\Console\Command;

class ModularListCommand extends Command
{
    protected $signature = 'modular:list';

    protected $description = 'Daftar semua modul.';

    public function handle(): int
    {
        $modules = [];

        foreach (config('modular.modules') as $name => $provider) {
            $modules[] = [$name, "modules/{$name}", $provider];
        }

        $this->table(['Name', 'Folder', 'Provider'], $modules);

        return self::SUCCESS;
    }
}
