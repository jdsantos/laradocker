<?php

namespace Jdsantos\Laradocker\Handlers;

use Illuminate\Support\Facades\File;
use Jdsantos\Laradocker\Contracts\StubConfigurator;
use Jdsantos\Laradocker\Contracts\StubProcessor;
use Jdsantos\Laradocker\Exceptions\InvalidConfiguration;

class ConcreteStubProcessor implements StubProcessor
{
    const STUBS_BASE_PATH = __DIR__.'/../Stubs';

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
     *
     * @throws InvalidConfiguration
     */
    public function process(): void
    {
        if (is_null($this->configurator)) {
            throw new InvalidConfiguration;
        }

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

        $dockerfileContent = $this->replaceLineWithWordInFile($stubPath, '{DATABASES}', $mergedDbStubContent);

        file_put_contents(self::STUBS_BASE_PATH.'/Dockerfile', $dockerfileContent);
    }

    public function copy(string $file): bool
    {
        $stubsBasePath = __DIR__.'/../Stubs';
        $projectBasePath = $this->configurator->getLaravelInstallationPath();

        $sourcePath = "$stubsBasePath/$file";
        $destPath = "$projectBasePath/$file";

        if (! File::exists("$sourcePath")) {
            return false;
        }

        if (File::exists("$destPath")) {
            if (File::isDirectory($destPath)) {
                File::deleteDirectory($destPath);
            }
        }

        if (File::isDirectory($sourcePath)) {
            return File::copyDirectory($sourcePath, $destPath);
        } else {
            return File::copy($sourcePath, $destPath);
        }

        return false;
    }

    public function cleanup(): void
    {
        $dockerfilePath = self::STUBS_BASE_PATH.'/Dockerfile';
        if (File::exists($dockerfilePath)) {
            File::delete($dockerfilePath);
        }
    }

    public function getStubFiles(): array
    {
        return self::FILES_TO_CONSIDER;
    }

    private function replaceLineWithWordInFile($filePath, $searchWord, $replacementString)
    {
        // Read the entire file into an array of lines
        $fileContents = file($filePath);

        // Iterate through each line
        foreach ($fileContents as &$line) {
            // Check if the line contains the search word
            if (strpos($line, $searchWord) !== false) {
                // Replace the entire line with the replacement string
                $line = $replacementString.PHP_EOL;
            }
        }

        return implode('', $fileContents);
    }
}
