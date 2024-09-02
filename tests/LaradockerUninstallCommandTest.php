<?php

namespace Jdsantos\Laradocker\Tests;

use Illuminate\Support\Facades\File;
use Jdsantos\Laradocker\Contracts\StubProcessor;
use Jdsantos\Laradocker\Handlers\ConcreteStubConfigurator;
use Jdsantos\Laradocker\Handlers\ConcreteStubProcessor;
use PHPUnit\Framework\Attributes\Test;

class LaradockerUninstallCommandTest extends TestCase
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

    #[Test]
    public function it_removes_all_created_files_and_directories(): void
    {
        // Mock the file and directory existence
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('isDirectory')->andReturn(false);
        File::shouldReceive('delete')->andReturn(true);
        File::shouldReceive('deleteDirectory')->andReturn(true);

        $this->artisan('laradocker:uninstall')
            ->expectsConfirmation('The following files will be deleted from your project folder. Do you wish to continue?', 'yes')
            ->run();

        $this->assertFilesDoNotExist($this->files);
    }

    #[Test]
    public function it_cancels_the_uninstall_action(): void
    {
        $this->artisan('laradocker:uninstall')
            ->expectsConfirmation('The following files will be deleted from your project folder. Do you wish to continue?', 'no')
            ->expectsOutputToContain('Canceled')
            ->run();
    }

    #[Test]
    public function it_does_not_fail_when_files_do_not_exist(): void
    {
        // Mock file existence to return false
        File::shouldReceive('exists')->andReturn(false);

        $this->artisan('laradocker:uninstall')
            ->expectsConfirmation('The following files will be deleted from your project folder. Do you wish to continue?', 'yes')
            ->run();

        // No files to check for non-existence since they are mocked as never having existed
    }

    #[Test]
    public function it_deletes_directories_if_they_exist(): void
    {
        // Mock the file and directory existence
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('deleteDirectory')->andReturn(true);
        File::shouldReceive('delete')->never();

        $this->artisan('laradocker:uninstall')
            ->expectsConfirmation('The following files will be deleted from your project folder. Do you wish to continue?', 'yes')
            ->run();

        // This test confirms that the command attempts to delete directories correctly
    }
}
