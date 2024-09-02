<?php

namespace Jdsantos\Laradocker\Tests;

class LaradockerInstallCommandTest extends TestCase
{
    public function testNoDatabasesInstallation()
    {
        $laravelPath = __DIR__ . '/../vendor/orchestra/testbench-core/laravel';
        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'yes')
            ->run();

        $filesToAssertExist = [$laravelPath . '/Dockerfile', $laravelPath . '/.dockerignore', $laravelPath . '/entrypoint.sh'];

        foreach ($filesToAssertExist as $fileToAssertExist) {
            $this->assertFileExists($fileToAssertExist);
        }
        $this->assertDirectoryExists($laravelPath . '/conf.d');
    }

    public function testSqliteInstallation()
    {
        $laravelPath = __DIR__ . '/../vendor/orchestra/testbench-core/laravel';
        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'yes')
            ->expectsQuestion('Which databases do you want to support?', ['sqlite'])
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'yes')
            ->run();

        $filesToAssertExist = [$laravelPath . '/Dockerfile', $laravelPath . '/.dockerignore', $laravelPath . '/entrypoint.sh'];

        foreach ($filesToAssertExist as $fileToAssertExist) {
            $this->assertFileExists($fileToAssertExist);
        }
        $this->assertDirectoryExists($laravelPath . '/conf.d');
    }
}
