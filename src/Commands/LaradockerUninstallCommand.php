<?php

namespace Jdsantos\Laradocker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Jdsantos\Laradocker\Contracts\StubProcessor;

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

    public function __construct(private StubProcessor $processor)
    {
        parent::__construct();
    }

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
     * Deletes any created files by this app
     */
    private function deleteExistingConfigurations(): void
    {
        $projectBasePath = $this->laravel->basePath();

        $filesToDelete = $this->processor->getStubFiles();

        $filesToDelete = array_map(function ($file) use ($projectBasePath) {
            return "$projectBasePath/$file";
        }, $filesToDelete);

        $this->line('');
        $this->line('The following files will be deleted:');
        $this->line('');

        foreach ($filesToDelete as $fileToDelete) {
            $this->line("<options=bold;fg=red> • $fileToDelete</>");
        }

        if ($this->confirm(question: 'The following files will be deleted from your project folder. Do you wish to continue?', default: true)) {
            foreach ($filesToDelete as $fileToDelete) {
                if (File::exists($fileToDelete)) {
                    if (File::isDirectory($fileToDelete)) {
                        File::deleteDirectory($fileToDelete);
                    } else {
                        File::delete($fileToDelete);
                    }
                }
                $this->line("<options=bold;fg=red> • $fileToDelete</>");
            }
            $this->line('');
            $this->line('<options=bold;fg=green>Uninstalled successfully.</>');
            $this->line('');
        } else {
            $this->line('');
            $this->error('Canceled. No files were deleted.');
            $this->line('');
        }
    }
}
