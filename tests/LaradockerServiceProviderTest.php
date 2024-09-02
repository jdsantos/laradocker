<?php

namespace Jdsantos\Laradocker\Tests\Providers;

use Jdsantos\Laradocker\Commands\LaradockerInstallCommand;
use Jdsantos\Laradocker\Commands\LaradockerUninstallCommand;
use Jdsantos\Laradocker\Providers\LaradockerServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LaradockerServiceProviderTest extends TestCase
{
    /**
     * Test the provides method of LaradockerServiceProvider.
     *
     * @return void
     */
    #[Test]
    public function it_provides_methods()
    {
        // Create an instance of the service provider
        $serviceProvider = new LaradockerServiceProvider($this->app);

        // Call the provides method
        $provides = $serviceProvider->provides();

        // Assert the provides method returns the expected array
        $this->assertIsArray($provides, 'The provides method should return an array.');
        $this->assertContains(LaradockerInstallCommand::class, $provides, 'The provides method should contain LaradockerInstallCommand.');
        $this->assertContains(LaradockerUninstallCommand::class, $provides, 'The provides method should contain LaradockerUninstallCommand.');
        $this->assertCount(2, $provides, 'The provides method should return exactly 2 services.');
    }

    /**
     * Get the package providers needed for this test.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaradockerServiceProvider::class,
        ];
    }
}
