<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager;

use KrisKuiper\FileManager\Contracts\DirectoryInterface;
use KrisKuiper\FileManager\Contracts\FileInterface;
use KrisKuiper\FileManager\Contracts\FileMutatorInterface;
use KrisKuiper\FileManager\Contracts\OwnerInterface;
use KrisKuiper\FileManager\Exceptions\DirectoryException;
use KrisKuiper\FileManager\Exceptions\FileException;

class File implements FileInterface, OwnerInterface, FileMutatorInterface
{
    /**
     * Contains the file as a string
     */
    private ?string $file;


    public function __construct(string $file = null)
    {
        $this->file = $file;
    }

    /**
     * @inheritdoc
     */
    public function setFile(string $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isReadable(): bool
    {
        return true === is_readable($this->file);
    }

    /**
     * @inheritdoc
     */
    public function isWritable(): bool
    {
        return true === is_writable($this->file);
    }

    /**
     * @inheritdoc
     */
    public function getMimeType(): string {
        return MimeType::get($this->getExtension());
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): bool
    {
        return $this->rename($name . ($this->getExtension() ? '.' . $this->getExtension() : null));
    }

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return pathinfo($this->file, PATHINFO_FILENAME);
    }

    /**
     * @inheritdoc
     */
    public function setBaseName(string $name): bool
    {
        return $this->rename($name);
    }

    /**
     * @inheritdoc
     */
    public function getBaseName(): ?string
    {
        return pathinfo($this->file, PATHINFO_BASENAME);
    }

    /**
     * @inheritdoc
     */
    public function setExtension(string $extension): bool
    {
        $name = substr($this->getBaseName(), 0, 0 - strlen($this->getExtension())) . ltrim($extension, '.');
        return $this->rename($name);
    }

    /**
     * @inheritdoc
     */
    public function getExtension(): ?string
    {
        $extension = pathinfo($this->file, PATHINFO_EXTENSION);

        if(null === $extension) {
            return null;
        }

        return strtolower($extension);
    }

    /**
     * @inheritdoc
     */
    public function getFileSize(): ?int
    {
        if(true === $this->exists()) {

            clearstatcache();
            return @filesize($this->file);
        }

        return 0;
    }

    /**
     * @inheritdoc
     */
    public function getPath(): ?string
    {
        return $this->file;
    }

    /**
     * @inheritdoc
     */
    public function getBasePath(): ?string
    {
        return dirname($this->file) . DIRECTORY_SEPARATOR;
    }

    /**
     * @inheritdoc
     * @throws DirectoryException
     */
    public function setBasePath(DirectoryInterface $directory): bool
    {
        return $this->move($directory);
    }

    /**
     * @inheritdoc
     */
    public function setModificationTime(int $time): bool
    {
        $touched = false;

        if(true === $this->exists()) {

            $touched = touch($this->file, $time, $this->getAccessTime());
            clearstatcache();
        }

        return $touched;
    }

    /**
     * @inheritdoc
     */
    public function getModificationTime(): bool|int
    {
        if(false === $this->isReadable()) {
            return false;
        }

        return @filemtime($this->file);
    }

    /**
     * @inheritdoc
     */
    public function setOwnerId(int $id): bool
    {
        return $this->chown($id);
    }

    /**
     * @inheritdoc
     */
    public function getOwnerId(): bool|int
    {
        $owner = @fileowner($this->file);

        if(false !== $owner) {
            return $owner;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function setOwner(string $name): bool
    {
        if(false !== $this->exists()) {
            return chown($this->file, $name);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getOwner(): bool|string
    {
        $id = $this->getOwnerId();

        if(false !== $id && $owner = @posix_getpwuid($id)) {
            return $owner['name'] ?? false;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getGroupId(): bool|int
    {
        return @filegroup($this->file);
    }

    /**
     * @inheritdoc
     */
    public function setGroupId(int $id): bool
    {
        return true === @chgrp($this->file, $id);
    }

    /**
     * @inheritdoc
     */
    public function setAccessTime(int $time): bool
    {
        $touched = false;

        if(true === $this->exists()) {

            $touched = touch($this->file, $this->getModificationTime(), $time);
            clearstatcache();
        }

        return $touched;
    }

    /**
     * @inheritdoc
     */
    public function getAccessTime(): bool|int
    {
        return @fileatime($this->file);
    }

    /**
     * @inheritdoc
     */
    public function getWidth(): ?int
    {
        if(@exif_imagetype($this->file) > 0) {

            $size = getimagesize($this->file);
            return $size[0];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getHeight(): ?int
    {
        if(@exif_imagetype($this->file) > 0) {

            $size = getimagesize($this->file);
            return $size[1];
        }

        return null;
    }

    /**
     * @inheritdoc
     * @throws DirectoryException
     */
    public function move(DirectoryInterface $directory): bool
    {
        if(false === $directory->isWritable()) {
            throw DirectoryException::directoryNotWritable($directory->getPath());
        }

        if(false !== $this->exists() && @rename($this->file, $directory->getPath() . $this->getBaseName())) {

            $this->file = $directory->getPath() . $this->getBaseName();
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function delete(): bool
    {
        if(false !== $this->exists()) {
            return @unlink($this->file);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function create(int $mode = 0555): bool
    {
        if(false === $this->exists()) {

            $success = false !== @fopen($this->file, 'wb+');
            $this->chmod($mode);
            return $success;
        }

        return true;
    }

    /**
     * @inheritdoc
     * @throws DirectoryException
     */
    public function copy(DirectoryInterface $directory, ?string $name = null): bool
    {
        if(false === $directory->isWritable()) {
            throw DirectoryException::directoryNotWritable($directory->getPath());
        }

        if(false !== $this->exists()) {
            return copy($this->file, $directory->getPath() . ($name ?? $this->getBaseName()));
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function rename(string $name): bool
    {
        if(false !== $this->exists()) {

            $file = $this->getBasePath() . $name;

            if(rename($this->file, $file)) {

                $this->file = $file;
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     * @throws FileException
     */
    public function getContent(): ?string
    {
        if(false !== $this->exists()) {

            if(false === $this->isReadable()) {
                throw FileException::fileNotReadable($this->file);
            }

            return file_get_contents($this->file);
        }

        return null;
    }

    /**
     * @inheritdoc
     * @throws FileException
     */
    public function append(string $data): bool
    {
        if(false !== $this->exists()) {

            if(false === $this->isWritable()) {
                throw FileException::fileNotWritable($this->file);
            }

            $handle  = fopen($this->file, 'ab');
            $written = fwrite($handle, $data);
            fclose($handle);

            return false !== $written;
        }

        return false;
    }

    /**
     * @inheritdoc
     * @throws FileException
     */
    public function prepend(string $data): self
    {
        if(false !== $this->exists()) {

            if(false === $this->isWritable()) {
                throw FileException::fileNotWritable($this->file);
            }

            clearstatcache();

            $handle = fopen($this->file, 'rb+');
            $length = strlen($data);
            $finalLength = filesize($this->file) + $length;
            $cache = fread($handle, $length);
            $i = 1;

            rewind($handle);

            while(ftell($handle) < $finalLength) {

                fwrite($handle, $data);

                $data = $cache;
                $cache = fread($handle, $length);

                fseek($handle, $i * $length);
                $i++;
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function exists(): bool
    {
        return true === is_file($this->file);
    }

    /**
     * @inheritdoc
     * @throws FileException
     */
    public function flush(): bool
    {
        $flushed = false;

        if(false !== $this->exists()) {

            if(false === $this->isWritable()) {
                throw FileException::fileNotWritable($this->file);
            }

            $fh = fopen($this->file, 'wb');

            if(flock($fh, LOCK_EX)) {

                ftruncate($fh, 0);
                $flushed = fflush($fh);
                flock($fh, LOCK_UN);
            }

            fclose($fh);
        }

        return $flushed;
    }

    /**
     * @inheritdoc
     */
    public function chown(int $userId): bool
    {
        if(false !== $this->exists()) {
            return chown($this->file, $userId);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function chmod(int|string $mode): bool
    {
        if(false !== $this->exists()) {
            return chmod($this->file, $mode);
        }

        return false;
    }
}