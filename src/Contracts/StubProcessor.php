<?php

namespace Jdsantos\Laradocker\Contracts;

interface StubProcessor
{
    public function getStubFiles(): array;

    public function process(): void;

    public function copy(string $file): bool;

    public function cleanup(): void;
}
