<?php

namespace Jdsantos\Laradocker\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Jdsantos\Laradocker\Commands\LaradockerInstallCommand;
use Jdsantos\Laradocker\Commands\LaradockerUninstallCommand;

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
            $this->commands([
                LaradockerInstallCommand::class,
                LaradockerUninstallCommand::class,
            ]);

            AboutCommand::add('Laradocker', function () {
                $currentProjectPath = $this->app->basePath();

                return [
                    'Version' => '1.0.3',
                    'Install status' => File::exists("$currentProjectPath/Dockerfile"),
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
