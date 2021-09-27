<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager\Contracts;

use KrisKuiper\FileManager\Collections\Collection;

interface DirectoryInterface
{
    /**
     * Returns the directory name
     */
    public function getName(): ?string;

    /**
     * Returns the path of the directory
     */
    public function getPath(): ?string;

    /**
     * Returns the parent path of a directory
     */
    public function getBasePath(): ?string;

    /**
     * Returns the modification time
     */
    public function getModificationTime(): bool|int;

    /**
     * Returns the last access time of a directory
     */
    public function getAccessTime(): bool|int;

    /**
     * Returns total size in bytes of all the files (recursive) in the current directory
     */
    public function getSize(): int;

    /**
     * Returns if current directory is readable
     */
    public function isReadable(): bool;

    /**
     * Returns if current directory is writable
     */
    public function isWritable(): bool;

    /**
     * Returns files and folders about the current directory in a collection
     */
    public function getContent(): Collection;

    /**
     * Returns if directory exists or not
     */
    public function exists(): bool;
}