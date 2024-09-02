<?php

namespace Jdsantos\Laradocker\Tests;

use Illuminate\Support\Facades\File;
use Jdsantos\Laradocker\Contracts\StubProcessor;
use Jdsantos\Laradocker\Handlers\ConcreteStubConfigurator;
use Jdsantos\Laradocker\Handlers\ConcreteStubProcessor;
use PHPUnit\Framework\Attributes\Test;

class LaradockerInstallCommandTest extends TestCase
{
    protected StubProcessor $processor;

    protected array $files;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the Concrete stub processor
        $this->processor = new ConcreteStubProcessor(new ConcreteStubConfigurator($this->app));
        $this->files = $this->processor->getStubFiles();
    }

    protected function tearDown(): void
    {
        $this->deleteAllFiles();
        parent::tearDown();
    }

    #[Test]
    public function it_can_install_without_additional_databases()
    {
        $this->runInstallationWithConfirmation(false);

        $this->assertFilesExist($this->files);

        $this->assertDirectoryExists($this->laravelPath.'/conf.d');
    }

    #[Test]
    public function it_can_install_with_sqlite_support()
    {
        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'yes')
            ->expectsQuestion('Which databases do you want to support?', ['sqlite'])
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'yes')
            ->run();

        $this->assertFilesExist($this->files);

        $this->assertDirectoryExists($this->laravelPath.'/conf.d');
    }

    #[Test]
    public function it_doesnt_install_without_confirmation()
    {
        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'no')
            ->run();

        $this->assertFilesDoNotExist($this->files);

        $this->assertDirectoryDoesNotExist($this->laravelPath.'/conf.d');
    }

    #[Test]
    protected function runInstallationWithConfirmation(bool $supportDatabases)
    {
        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', $supportDatabases ? 'yes' : 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'yes')
            ->run();
    }

    private function deleteAllFiles(): void
    {
        $files = $this->processor->getStubFiles();
        foreach ($files as $file) {
            $path = $this->laravelPath."/$file";
            if (File::exists($path)) {
                File::isDirectory($path) ? File::deleteDirectory($path) : File::delete($path);
            }
        }
    }
}
