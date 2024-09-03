<?php

namespace Jdsantos\Laradocker\Handlers;

use Illuminate\Support\Facades\File;
use Jdsantos\Laradocker\Contracts\StubConfigurator;
use Jdsantos\Laradocker\Contracts\StubProcessor;
use Jdsantos\Laradocker\Helpers\StubFileHelper;

class ConcreteStubProcessor implements StubProcessor
{
    /**
     * The base path for the Stubs folder
     */
    const STUBS_BASE_PATH = __DIR__.'/../Stubs';

    /**
     * The list of files to consider as a deployable stub
     */
    const FILES_TO_CONSIDER = [
        'Dockerfile',
        '.dockerignore',
        'entrypoint.sh',
        'conf.d',
    ];

    public function __construct(private StubConfigurator $configurator) {}

    /**
     * Executes the processor of stubs and handles files to be generated from templates
     *
     * @return array
     */
    public function process(): void
    {
        $stubPath = self::STUBS_BASE_PATH.'/Dockerfile.stub';

        // Include databases installation steps
        $databases = $this->configurator->getDatabasesToSupport();

        $dbStubsContent = [];
        foreach ($databases as $database) {
            $dbStubPath = self::STUBS_BASE_PATH."/databases/$database.stub";
            if (File::exists($dbStubPath)) {
                $dbStubsContent[] = File::get($dbStubPath);
            }
        }

        $mergedDbStubContent = implode("\n\n", $dbStubsContent);
        $dockerfileContent = StubFileHelper::replaceLineInFile($stubPath, '{DATABASES}', $mergedDbStubContent);
        File::put(self::STUBS_BASE_PATH.'/Dockerfile', $dockerfileContent);
    }

    /**
     * Copies a file from the Stubs location to the configured installation path
     */
    public function copy(string $file): bool
    {
        $stubsBasePath = self::STUBS_BASE_PATH;
        $projectBasePath = $this->configurator->getLaravelInstallationPath();

        $sourcePath = "$stubsBasePath/$file";
        $destPath = "$projectBasePath/$file";

        if (! File::exists("$sourcePath")) {
            return false;
        }

        // If file exists in the destiny, and happens to be a directory, remove it first
        if (File::exists("$destPath") && File::isDirectory($destPath)) {
            File::deleteDirectory($destPath);
        }

        if (File::isDirectory($sourcePath)) {
            return File::copyDirectory($sourcePath, $destPath);
        } else {
            return File::copy($sourcePath, $destPath);
        }

        return false;
    }

    /**
     * Does some housekeeping by deleting generated files in the Stubs folder
     */
    public function cleanup(): void
    {
        // Delete the generated Dockerfile file
        $dockerfilePath = self::STUBS_BASE_PATH.'/Dockerfile';
        if (File::exists($dockerfilePath)) {
            File::delete($dockerfilePath);
        }
    }

    /**
     * Returns the list of files processed/handled by this processor
     */
    public function getStubFiles(): array
    {
        return self::FILES_TO_CONSIDER;
    }
}
