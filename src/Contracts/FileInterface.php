<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager\Contracts;

interface FileInterface
{
    /**
     * Sets the current file to work with
     */
    public function setFile(string $file): self;

    /**
     * Returns if the current file is readable
     */
    public function isReadable(): bool;

    /**
     * Returns if the current file is writable
     */
    public function isWritable(): bool;

    /**
     * Returns mime type of current file
     */
    public function getMimeType(): string;

    /**
     * Returns the name of the file without extension
     */
    public function getName(): ?string;

    /**
     * Returns the name of the file with extension
     */
    public function getBaseName(): ?string;

    /**
     * Returns the extension of the file
     */
    public function getExtension(): ?string;

    /**
     * Returns the file size in bytes
     */
    public function getFileSize(): ?int;

    /**
     * Returns the path of the file
     */
    public function getPath(): ?string;

    /**
     * Returns the parent directory of the file
     */
    public function getBasePath(): ?string;

    /**
     * Returns the modification time
     */
    public function getModificationTime(): bool|int;

    /**
     * Returns the last access time of a file
     */
    public function getAccessTime(): bool|int;

    /**
     * Returns the width of the file (i.e. image)
     */
    public function getWidth(): ?int;

    /**
     * Returns the height of the file (i.e. image)
     */
    public function getHeight(): ?int;

    /**
     * Returns the content of current file if exists
     */
    public function getContent(): ?string;

    /**
     * Checks whether the current file exists
     */
    public function exists(): bool;
}