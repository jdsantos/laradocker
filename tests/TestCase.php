<?php

namespace Jdsantos\Laradocker\Tests;

use Jdsantos\Laradocker\Providers\LaradockerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return [
            LaradockerServiceProvider::class,
        ];
    }
}
