<?php

namespace Jdsantos\Laradocker\Handlers;

use Illuminate\Contracts\Foundation\Application;
use Jdsantos\Laradocker\Contracts\StubConfigurator;

class ConcreteStubConfigurator implements StubConfigurator
{
    private array $databases = [];

    const SUPPORTED_DATABASES = ['sqlite', 'mysql', 'mariadb', 'pgsql'];

    public function __construct(private Application $app) {}

    public function getAllPossibleDatabasesToSupport(): array
    {
        return self::SUPPORTED_DATABASES;
    }

    public function getLaravelInstallationPath(): string
    {
        return $this->app->basePath();
    }

    public function addDatabaseSupportFor(string $databaseIdentifier): void
    {
        if (! in_array($databaseIdentifier, $this->databases)) {
            $this->databases[] = $databaseIdentifier;
        }
    }

    public function getDatabasesToSupport(): array
    {
        return $this->databases;
    }
}
