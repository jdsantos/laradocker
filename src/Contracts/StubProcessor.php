<?php

namespace Jdsantos\Laradocker\Contracts;

/**
 * The interface that allows a collection of stubs to be processed and deployed to
 * the user's project
 */
interface StubProcessor
{
    public function getStubFiles(): array;

    public function process(): void;

    public function copy(string $file): bool;

    public function cleanup(): void;
}
