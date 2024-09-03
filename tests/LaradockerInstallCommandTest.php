<?php

namespace Jdsantos\Laradocker\Tests;

use Illuminate\Support\Facades\File;
use Jdsantos\Laradocker\Contracts\StubConfigurator;
use Jdsantos\Laradocker\Contracts\StubProcessor;
use Jdsantos\Laradocker\Handlers\ConcreteStubConfigurator;
use Jdsantos\Laradocker\Handlers\ConcreteStubProcessor;
use PHPUnit\Framework\Attributes\Test;

class LaradockerInstallCommandTest extends TestCase
{
    protected StubProcessor $processor;

    protected StubConfigurator $configurator;

    protected array $files;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the Concrete stub processor
        $this->configurator = $this->app->make(ConcreteStubConfigurator::class);
        $this->processor = new ConcreteStubProcessor($this->configurator);
        $this->files = $this->processor->getStubFiles();
    }

    protected function tearDown(): void
    {
        $this->deleteAllFiles();
        parent::tearDown();
    }

    #[Test]
    public function it_can_install_without_additional_databases(): void
    {
        $this->runInstallationWithConfirmation(false);

        $this->assertFilesExist($this->files);

        $this->assertDirectoryExists($this->laravelPath.'/conf.d');
    }

    #[Test]
    public function it_can_install_with_sqlite_support(): void
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
    public function it_cleans_up_generated_files(): void
    {

        // File::shouldReceive('exists')->andReturn(true);
        // File::shouldReceive('delete')->andReturn(true);

        // Run cleanup
        $this->processor->cleanup();

        $this->assertFileDoesNotExist(__DIR__.'/../src/Stubs/Dockerfile');
    }

    #[Test]
    public function it_copies_files_correctly(): void
    {
        // Mock File facade methods for both file and directory scenarios
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('deleteDirectory')->andReturn(true);
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('copyDirectory')->andReturn(true);

        // Copy directory
        $result = $this->processor->copy('conf.d');
        $this->assertTrue($result);

        // Mock for a file
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('exists')->andReturn(false);
        File::shouldReceive('isDirectory')->andReturn(false);
        File::shouldReceive('copy')->andReturn(true);

        // Copy file
        $result = $this->processor->copy('Dockerfile');
        $this->assertTrue($result);
    }

    #[Test]
    public function it_doesnt_install_without_confirmation(): void
    {
        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'no')
            ->run();

        $this->assertFilesDoNotExist($this->files);

        $this->assertDirectoryDoesNotExist($this->laravelPath.'/conf.d');
    }

    #[Test]
    public function it_processes_without_databases(): void
    {
        // Mock the configurator to return an empty array of databases
        $this->mock(ConcreteStubConfigurator::class, function ($mock) {
            $mock->shouldReceive('getDatabasesToSupport')->andReturn([]);
            $mock->shouldReceive('getLaravelInstallationPath')->andReturn($this->laravelPath);
        });

        $processor = new ConcreteStubProcessor(app(ConcreteStubConfigurator::class));

        // Run process method
        $processor->process();

        // Assert that Dockerfile is created without database stubs
        $this->assertFileExists(ConcreteStubProcessor::STUBS_BASE_PATH.'/Dockerfile');
    }

    #[Test]
    public function it_processes_with_non_existent_database_stubs(): void
    {
        // Mock the configurator to return specific databases
        $this->mock(ConcreteStubConfigurator::class, function ($mock) {
            $mock->shouldReceive('getDatabasesToSupport')->andReturn(['nonexistent-db']);
            $mock->shouldReceive('getLaravelInstallationPath')->andReturn($this->laravelPath);
        });

        File::shouldReceive('exists')
            ->andReturn(false);
        File::shouldReceive('get')->never(); // Should not attempt to get a file that doesn't exist
        File::shouldReceive('put');

        $processor = new ConcreteStubProcessor(app(ConcreteStubConfigurator::class));

        // Run process method
        $processor->process();

        // Assert that Dockerfile is created without database stubs
        $this->assertFileExists(ConcreteStubProcessor::STUBS_BASE_PATH.'/Dockerfile');
    }

    #[Test]
    public function it_does_not_copy_nonexistent_files(): void
    {
        // Mock the file facade to simulate file does not exist
        File::shouldReceive('exists')->andReturn(false);

        // Copy should fail
        $result = $this->processor->copy('nonexistentfile');
        $this->assertFalse($result);
    }

    #[Test]
    public function it_copies_directory_correctly(): void
    {
        // Mock the file facade for directory copy
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('deleteDirectory')->andReturn(true);
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('copyDirectory')->andReturn(true);

        // Copy directory
        $result = $this->processor->copy('conf.d');
        $this->assertTrue($result);
    }

    #[Test]
    protected function runInstallationWithConfirmation(bool $supportDatabases): void
    {
        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', $supportDatabases ? 'yes' : 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'yes')
            ->run();
    }

    #[Test]
    public function it_collects_console_options_correctly()
    {
        // Mock the configurator to return an empty array of databases
        $this->mock(ConcreteStubConfigurator::class, function ($mock) {
            $mock->shouldReceive('addDatabaseSupportFor')->twice();
            $mock->shouldReceive('getDatabasesToSupport')->andReturn(['mysql', 'sqlite']);
            $mock->shouldReceive('getLaravelInstallationPath')->andReturn($this->laravelPath);
        });

        $configurator = app(ConcreteStubConfigurator::class);

        $this->artisan('laradocker:install', ['--database' => ['mysql', 'sqlite']])
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'yes')
            ->run();

        // // Assert configurator is called with correct databases
        $this->assertEquals(['mysql', 'sqlite'], $configurator->getDatabasesToSupport());
    }

    #[Test]
    public function it_confirms_database_choices_correctly()
    {
        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'yes')
            ->expectsChoice('Which databases do you want to support?', ['sqlite'], ConcreteStubConfigurator::SUPPORTED_DATABASES)
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'no')
            ->expectsConfirmation('Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', 'yes')
            ->run();
    }

    #[Test]
    public function it_handles_process_and_copy_stubs()
    {
        // Mock all necessary methods for the processor
        $this->mock(ConcreteStubProcessor::class, function ($mock) {
            $mock->shouldReceive('getStubFiles')->andReturn(['Dockerfile', 'conf.d']);
            $mock->shouldReceive('process')->once();
            $mock->shouldReceive('copy')->twice()->andReturn(true);
            $mock->shouldReceive('cleanup')->once();
        });

        $this->artisan('laradocker:install')
            ->expectsConfirmation('Do you want your image support to (more) databases?', 'no')
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
