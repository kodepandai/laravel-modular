<?php

use KodePandai\Modular\Module;

test('kelas module sukses handle semuanya', function () {
    $module = new Module();

    expect($module->name('Sales'))
        ->name->toBe('Sales');

    expect($module->hasConfigFile('c-1'))
        ->configFileNames->toBe(['c-1']);

    expect($module->hasConfigFiles(['c-2', 'c-3']))
        ->configFileNames->toBe(['c-1', 'c-2', 'c-3']);

    expect($module)
        ->hasViews->toBeFalse()
        ->hasViews()->hasViews->toBeTrue();

    // TODO: tes semua fungsi dalam kelas module
});
