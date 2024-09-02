<?php

namespace Jdsantos\Laradocker\Helpers;

use Illuminate\Support\Facades\File;

class DockerFileInspectionHelper
{
    private function __construct(private string $path) {}

    public static function fromPath(string $path): self
    {
        return new self($path);
    }

    public function inspect(): array
    {
        $version = 'UNDETECTED';
        $installed = false;

        if (File::exists($this->path)) {
            $dockerfileContent = File::get($this->path);

            // Define a regular expression to match the laradocker.version value
            preg_match('/LABEL\s+laradocker\.version="([^"]+)"/', $dockerfileContent, $matches);
            if (isset($matches[1])) {
                $version = $matches[1];
                $installed = true;
            }
        }

        return [
            'Version' => $version,
            'Status' => $installed ? '<options=bold;fg=green>INSTALLED</>' : '<options=bold;fg=red>NOT INSTALLED</>',
        ];
    }
}
