<?php

namespace Jdsantos\Laradocker\Providers;

use Illuminate\Foundation\Console\AboutCommand;
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

            AboutCommand::add('Laradocker', fn () => ['Version' => '1.0.0']);
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
