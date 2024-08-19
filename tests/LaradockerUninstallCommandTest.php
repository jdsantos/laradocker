<?php

namespace Jdsantos\Laradocker\Tests;

class LaradockerUninstallCommandTest extends TestCase
{
    public function testHandle()
    {
        $laravelPath = __DIR__.'/../vendor/orchestra/testbench-core/laravel';
        $this->artisan('laradocker:uninstall')->run();

        $filesToAssertMissing = [$laravelPath.'/Dockerfile', $laravelPath.'/.dockerignore', $laravelPath.'/.dockerignore'];

        foreach ($filesToAssertMissing as $fileToAssertMissing) {
            $this->assertFileDoesNotExist($fileToAssertMissing);
        }
        $this->assertDirectoryDoesNotExist($laravelPath.'/conf.d');
    }
}
