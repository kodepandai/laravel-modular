<?php

namespace KodePandai\Modular\Tests;

use KodePandai\Modular\Providers\PackageServiceProvider;

/**
 * @see https://packages.tools/testbench/basic/testcase.html
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getPackageProviders($app): array
    {
        return [ PackageServiceProvider::class ];
    }
}
