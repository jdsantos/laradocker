<?php

namespace Jdsantos\Laradocker\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Jdsantos\Laradocker\Commands\LaradockerInstallCommand;
use Jdsantos\Laradocker\Commands\LaradockerUninstallCommand;
use Jdsantos\Laradocker\Contracts\StubConfigurator;
use Jdsantos\Laradocker\Contracts\StubProcessor;
use Jdsantos\Laradocker\Handlers\ConcreteStubConfigurator;
use Jdsantos\Laradocker\Handlers\ConcreteStubProcessor;

class LaradockerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->app->singleton(StubConfigurator::class, function (Application $app) {
                return $app->make(ConcreteStubConfigurator::class);
            });
            $this->app->bind(StubProcessor::class, ConcreteStubProcessor::class);

            $this->commands([
                LaradockerInstallCommand::class,
                LaradockerUninstallCommand::class,
            ]);

            AboutCommand::add('Laradocker', function () {
                $currentProjectPath = $this->app->basePath();

                return [
                    'Version' => '1.0.3',
                    'Status' => File::exists("$currentProjectPath/Dockerfile") ? '<options=bold;fg=green>INSTALLED</>' : '<options=bold;fg=red>NOT INSTALLED</>',
                ];
            });
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            LaradockerInstallCommand::class,
            LaradockerUninstallCommand::class,
        ];
    }
}
