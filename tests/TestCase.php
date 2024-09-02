<?php

namespace Jdsantos\Laradocker\Tests;

use Illuminate\Support\Facades\File;
use Jdsantos\Laradocker\Providers\LaradockerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    // Define a class property for the Laravel path
    protected string $laravelPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the Laravel path in the setup method
        $this->laravelPath = __DIR__.'/../vendor/orchestra/testbench-core/laravel';
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return [
            LaradockerServiceProvider::class,
        ];
    }

    protected function assertFilesExist(array $files): void
    {
        foreach ($files as $file) {
            $path = $this->laravelPath."/$file";
            if (File::isDirectory($path)) {
                $this->assertDirectoryExists($path);
            } else {
                $this->assertFileExists($path);
            }
        }
    }

    protected function assertFilesDoNotExist(array $files): void
    {
        foreach ($files as $file) {
            $path = $this->laravelPath."/$file";
            if (File::exists($path)) {
                if (File::isDirectory($path)) {
                    $this->assertDirectoryDoesNotExist($path);
                } else {
                    $this->assertFileDoesNotExist($path);
                }
            }
        }
    }
}
