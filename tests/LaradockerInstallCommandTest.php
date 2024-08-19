<?php

namespace Jdsantos\Laradocker\Tests;

class LaradockerInstallCommandTest extends TestCase
{
    public function testHandle()
    {
        $laravelPath = __DIR__.'/../vendor/orchestra/testbench-core/laravel';
        $this->artisan('laradocker:install')->run();

        $filesToAssertExist = [$laravelPath.'/Dockerfile', $laravelPath.'/.dockerignore', $laravelPath.'/entrypoint.sh'];

        foreach ($filesToAssertExist as $fileToAssertExist) {
            $this->assertFileExists($fileToAssertExist);
        }
        $this->assertDirectoryExists($laravelPath.'/conf.d');
    }
}
