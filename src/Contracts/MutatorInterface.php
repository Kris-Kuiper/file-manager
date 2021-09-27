<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager\Contracts;

interface MutatorInterface
{
    /**
     * Sets a new name (renaming) for the current directory
     */
    public function setName(string $name): bool;

    /**
     * Sets the path of the file
     */
    public function setBasePath(DirectoryInterface $directory): bool;

    /**
     * Sets the modification time
     */
    public function setModificationTime(int $time): bool;

    /**
     * Sets the access time of a directory
     */
    public function setAccessTime(int $time): bool;

    /**
     * Moves the current directory to a new directory including its content
     */
    public function move(DirectoryInterface $directory): bool;

    /**
     * Deletes the current directory with content
     */
    public function delete(): bool;

    /**
     * Makes a copy of the directory or file to the destination directory
     * if the file already exists, it will be overwritten
     * if the directory already exists, it will be merged
     */
    public function copy(DirectoryInterface $directory, ?string $name = null): bool;

    /**
     * Renames the current file or directory
     */
    public function rename(string $name): bool;

    /**
     * Removes all files and directories within the current directory, but leaves the current directory intact
     */
    public function flush(): bool;

    /**
     * Creates recursively (if necessary) new directory
     */
    public function create(int $mode = 0777): bool;
}
