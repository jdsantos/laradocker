<?php

namespace Jdsantos\Laradocker\Contracts;

/**
 * This class allows for changing and mocking the behaviour of the configurator,
 * if we need to add more options going forward.
 */
interface StubConfigurator
{
    public function getLaravelInstallationPath(): string;

    public function addDatabaseSupportFor(string $databaseIdentifier): void;

    public function getDatabasesToSupport(): array;

    public function getAllPossibleDatabasesToSupport(): array;
}
