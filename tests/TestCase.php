<?php

namespace KodePandai\Modular\Tests;

use KodePandai\Modular\ModularServiceProvider;

/**
 * @see https://packages.tools/testbench/basic/testcase.html
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [ModularServiceProvider::class];
    }
}
