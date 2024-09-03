<?php

namespace Jdsantos\Laradocker\Tests;

use Illuminate\Support\Facades\File;
use Jdsantos\Laradocker\Helpers\DockerfileInspectionHelper;
use PHPUnit\Framework\Attributes\Test;

class LaradockerAboutCommandTest extends TestCase
{
    #[Test]
    public function it_shows_laradocker_on_about()
    {
        $this->artisan('about')->expectsOutputToContain('Laradocker');
    }

    #[Test]
    public function it_detects_version_in_dockerfile()
    {
        // Mock the File facade
        File::shouldReceive('exists')->once()->with('/path/to/Dockerfile')->andReturn(true);
        File::shouldReceive('get')->once()->with('/path/to/Dockerfile')->andReturn('LABEL laradocker.version="1.0.0"');

        $inspector = DockerfileInspectionHelper::fromPath('/path/to/Dockerfile');
        $inspection = $inspector->inspect();

        $this->assertEquals('1.0.0', $inspection['Version']);
        $this->assertStringContainsString('INSTALLED', $inspection['Status']);
    }

    #[Test]
    public function it_reports_undetected_version_when_dockerfile_has_no_version()
    {
        // Mock the File facade
        File::shouldReceive('exists')->once()->with('/path/to/Dockerfile')->andReturn(true);
        File::shouldReceive('get')->once()->with('/path/to/Dockerfile')->andReturn('FROM php:8.0');

        $inspector = DockerfileInspectionHelper::fromPath('/path/to/Dockerfile');
        $inspection = $inspector->inspect();

        $this->assertEquals('UNDETECTED', $inspection['Version']);
        $this->assertStringContainsString('NOT INSTALLED', $inspection['Status']);
    }

    #[Test]
    public function it_handles_missing_dockerfile()
    {
        // Mock the File facade
        File::shouldReceive('exists')->once()->with('/path/to/nonexistent/Dockerfile')->andReturn(false);

        $inspector = DockerfileInspectionHelper::fromPath('/path/to/nonexistent/Dockerfile');
        $inspection = $inspector->inspect();

        $this->assertEquals('UNDETECTED', $inspection['Version']);
        $this->assertStringContainsString('NOT INSTALLED', $inspection['Status']);
    }
}
