<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager;

use FilesystemIterator;
use KrisKuiper\FileManager\Collections\Collection;
use KrisKuiper\FileManager\Contracts\DirectoryInterface;
use KrisKuiper\FileManager\Contracts\MutatorInterface;
use KrisKuiper\FileManager\Contracts\OwnerInterface;
use KrisKuiper\FileManager\Exceptions\DirectoryException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Directory implements DirectoryInterface, OwnerInterface, MutatorInterface
{
    /**
     * Contains the current directory path
     */
    private string $directoryPath;


    public function __construct(string $directoryPath)
    {
        $this->directoryPath = rtrim($directoryPath, DIRECTORY_SEPARATOR);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): bool
    {
        return $this->rename($name);
    }

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        $name = explode(DIRECTORY_SEPARATOR, $this->directoryPath);
        return end($name);
    }

    /**
     * @inheritdoc
     */
    public function getPath(): ?string
    {
        return rtrim($this->directoryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * @inheritdoc
     */
    public function getBasePath(): ?string
    {
        return dirname($this->directoryPath) . DIRECTORY_SEPARATOR;
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

            $touched = touch($this->directoryPath, $time, $this->getAccessTime());
            clearstatcache();
        }

        return $touched;
    }

    /**
     * @inheritdoc
     */
    public function getModificationTime(): bool|int
    {
        return @filemtime($this->directoryPath);
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
        $owner = @fileowner($this->directoryPath);

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
            return chown($this->getPath(), $name);
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
    public function setAccessTime(int $time): bool
    {
        $touched = false;

        if(true === $this->exists()) {

            $touched = touch($this->directoryPath, $this->getModificationTime(), $time);
            clearstatcache();
        }

        return $touched;
    }

    /**
     * @inheritdoc
     */
    public function getAccessTime(): bool|int
    {
        return @fileatime($this->directoryPath);
    }

    /**
     * @inheritdoc
     */
    public function getGroupId(): bool|int
    {
        return @filegroup($this->directoryPath);
    }

    /**
     * @inheritdoc
     */
    public function setGroupId(int $id): bool
    {
        return true === @chgrp($this->directoryPath, $id);
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        $total = 0;
        $path  = realpath($this->getPath());

        if($path !== false) {

            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                $total += $object->getSize();
            }
        }

        return $total;
    }

    /**
     * @inheritdoc
     */
    public function isReadable(): bool
    {
        if(false !== $this->exists()) {
            return true === is_readable($this->directoryPath);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function isWritable(): bool
    {
        if(false !== $this->exists()) {
            return true === is_writable($this->directoryPath);
        }

        return false;
    }

    /**
     * @inheritdoc
     * @throws DirectoryException
     */
    public function getContent(): Collection
    {
        $path = $this->getPath();

        if(false === is_dir($path)) {
            throw DirectoryException::directoryNotExists($path);
        }

        $files = array_values(array_diff(scandir($path), ['.', '..']));
        $collection = new Collection();

        foreach($files as $file) {

            if(true === is_dir($path . $file)) {

                $collection->append(new self($file));
                continue;
            }

            $collection->append(new File($path . $file));
        }

        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function exists(): bool
    {
        return true === is_dir($this->directoryPath);
    }

    /**
     * @inheritdoc
     * @throws DirectoryException
     */
    public function move(DirectoryInterface $directory): bool
    {
        //Check if new directory is writable
        if(false === $directory->isWritable()) {
            throw DirectoryException::directoryNotWritable($directory->getPath());
        }

        if(false !== $this->exists() && @rename($this->getPath(), $directory->getPath() . $this->getName())) {

            $this->directoryPath = $directory->getPath(). $this->getName();
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

            $this->purge($this->getPath());
            @rmdir($this->getPath());
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function copy(DirectoryInterface $directory, ?string $name = null): bool
    {
        //Copy recursively the directory and files
        if(false !== $this->exists()) {

            $this->recursiveCopy($this->directoryPath, $directory->getPath());
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function rename(string $name): bool
    {
        if(false !== $this->exists()) {

            $path = $this->getBasePath() . DIRECTORY_SEPARATOR . $name;

            if(@rename($this->directoryPath, $path)) {

                $this->directoryPath = $path;
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function flush(): bool
    {
        return $this->purge($this->directoryPath);
    }

    /**
     * @inheritdoc
     */
    public function create(int $mode = 0777): bool
    {
        $paths = explode(DIRECTORY_SEPARATOR, $this->directoryPath);
        $build = '';

        foreach($paths as $path) {

            $build .= $path . DIRECTORY_SEPARATOR;

            if(false === @mkdir($build, $mode) && false === is_dir($build)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function chmod(int|string $mode): bool
    {
        if(false !== $this->exists()) {
            return chmod($this->getPath(), $mode);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function chown(int $userId): bool
    {
        if(false !== $this->exists()) {
            return chown($this->directoryPath, $userId);
        }

        return false;
    }

    /**
     * Copies all contents of directory to an existing directory
     */
    private function recursiveCopy(string $source, string $destination): void
    {
        if(false === is_dir($destination)) {
            (new Directory($destination))->create();
        }

        $directory = opendir($source);

        while(false !== ($file = readdir($directory))) {

            if(false === in_array($file, ['.', '..'])) {

                if(is_dir($source . DIRECTORY_SEPARATOR . $file) ) {
                    $this->recursiveCopy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
                }
                else {
                    copy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
                }
            }
        }

        closedir($directory);
    }

    /**
     * Removes all files and directories within a directory
     */
    private function purge(string $path): bool
    {
        $files = glob(rtrim($path,DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '{,.}*', GLOB_BRACE);

        foreach($files as $file) {

            if(substr($file, -1, 1) === '.' || substr($file, -2, 2) === '..') {
                continue;
            }

            if(true === is_file($file)) {
                @unlink($file);
            }
            elseif(true === is_dir($file)) {

                $this->purge($file);
                @rmdir($file);
            }
        }

        return true;
    }
}