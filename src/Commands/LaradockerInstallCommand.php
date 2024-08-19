<?php

namespace Jdsantos\Laradocker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LaradockerInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradocker:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install any necessary files to prepare this app for docker deployments';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->copyStubs();
    }

    /**
     * Copies stubs from the 'Stubs' folder to the current project's root location
     */
    private function copyStubs(): void
    {
        $projectBasePath = $this->laravel->basePath();
        $stubsBasePath = __DIR__.'/../Stubs';

        // copy base docker files + entrypoint
        File::copy("$stubsBasePath/.dockerignore", "$projectBasePath/.dockerignore");
        File::copy("$stubsBasePath/Dockerfile", "$projectBasePath/Dockerfile");
        File::copy("$stubsBasePath/entrypoint.sh", "$projectBasePath/entrypoint.sh");

        if (File::exists("$projectBasePath/conf.d")) {
            File::deleteDirectory("$projectBasePath/conf.d");
        }
        // copy configuration files directory for nginx, php, pfp-fpm, supervisor, opcache
        File::copyDirectory("$stubsBasePath/conf.d", "$projectBasePath/conf.d");

        $this->info('installed successfully.');
    }
}