<?php

namespace Jdsantos\Laradocker\Contracts;

interface StubConfigurator
{
    public function getLaravelInstallationPath(): string;

    public function addDatabaseSupportFor(string $databaseIdentifier): void;

    public function getDatabasesToSupport(): array;

    public function getAllPossibleDatabasesToSupport(): array;
}
