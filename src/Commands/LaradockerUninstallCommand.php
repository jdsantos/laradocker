<?php

namespace Jdsantos\Laradocker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LaradockerUninstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradocker:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall any files created by laradocker';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->deleteExistingConfigurations();
    }

    /**
     * Copies stubs from the 'Stubs' folder to the current project's root location
     */
    private function deleteExistingConfigurations(): void
    {
        $projectBasePath = $this->laravel->basePath();

        $filesToDelete = ["$projectBasePath/conf.d", "$projectBasePath/Dockerfile", "$projectBasePath/.dockerignore", "$projectBasePath/entrypoint.sh"];

        foreach ($filesToDelete as $fileToDelete) {
            if (File::exists($fileToDelete)) {
                if (File::isDirectory($fileToDelete)) {
                    File::deleteDirectory($fileToDelete);
                } else {
                    File::delete($fileToDelete);
                }
            }
        }

        $this->info('uninstalled successfully.');
    }
}