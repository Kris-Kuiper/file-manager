<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager\Contracts;

interface FileMutatorInterface extends MutatorInterface
{
    /**
     * Sets the extension of the file
     */
    public function setExtension(string $extension): bool;

    /**
     * Appends data to current file
     */
    public function append(string $data): bool;

    /**
     * Prepends data to current file
     */
    public function prepend(string $data): self;

    /**
     * Empty the current file content
     */
    public function flush(): bool;

    /**
     * Renames the file
     */
    public function setBaseName(string $name): bool;
}