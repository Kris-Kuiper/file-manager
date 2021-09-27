<?php

declare(strict_types=1);

namespace Tests;

use FilesystemIterator;
use KrisKuiper\FileManager\Collections\Collection;
use KrisKuiper\FileManager\Exceptions\DirectoryException;
use KrisKuiper\FileManager\File;
use PHPUnit\Framework\TestCase;
use KrisKuiper\FileManager\Directory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class DirectoryTest extends TestCase
{
    /**
     * The path to the assets folder
     */
    private string $path;

    private Directory $directory;

    public function setUp(): void
    {
        $this->path = getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'directory'. DIRECTORY_SEPARATOR;
        $this->directory = new Directory($this->path . 'test');
        $this->__destruct();
    }

    public function __destruct()
    {
        if(false === is_dir($this->path)) {
            return;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {

            if(true === $file->isDir()){
                @rmdir($file->getRealPath());
            }
            else {
                @unlink($file->getRealPath());
            }
        }

        @rmdir($this->path);
    }

    /**
     * Testing creating a new directory
     * Testing if directory is readable
     * Testing if directory is writable
     * Testing if directory exists
     */
    public function testCreatingDirectory(): void
    {
        self::assertFalse($this->directory->isReadable());
        self::assertFalse($this->directory->isWritable());
        self::assertFalse($this->directory->exists());

        $this->directory->create();
        $this->directory->setOwner('administrator');
        $this->directory->setGroupId(1000);

        self::assertTrue($this->directory->isReadable());
        self::assertTrue($this->directory->isWritable());
        self::assertTrue($this->directory->exists());

        $this->directory->delete();
    }

    /**
     * Testing changing the name of a directory
     * @return void
     */
    public function testChangingName(): void
    {
        $this->directory->create();
        $this->directory->setName('new-name');

        self::assertEquals('new-name', $this->directory->getName());
        self::assertDirectoryExists($this->path . 'new-name');

        $this->directory->delete();
    }

    /**
     * Testing changing the extension of a directory
     * Testing appending text to a directory
     */
    public function testFileSize(): void
    {
        $this->directory->create();
        file_put_contents($this->directory->getPath() . 'test.txt', 'test');
        self::assertEquals(4, $this->directory->getSize());
        $this->directory->delete();
    }


    /**
     * Testing changing the modification time of a directory
     * @return void
     */
    public function testModificationTime(): void
    {
        $this->directory->create();
        self::assertEquals($this->directory->getModificationTime(), time());

        $this->directory->setModificationTime(time() - 1000);
        self::assertEquals($this->directory->getModificationTime(), time() - 1000);

        $this->directory->delete();
    }


    /**
     * Testing retrieving the parent directory
     * @return void
     * @throws DirectoryException
     */
    public function testBasePath(): void
    {
        $this->directory->create();
        self::assertEquals($this->directory->getBasePath(), $this->path);

        $directory = new Directory($this->path . 'parent');
        $directory->create();

        $this->directory->setBasePath($directory);
        self::assertEquals($this->directory->getBasePath(), $this->path . 'parent' . DIRECTORY_SEPARATOR);

        $directory->delete();
    }


    /**
     * Testing changing the access time of a directory
     * @return void
     */
    public function testAccessTime(): void
    {
        $this->directory->create();
        self::assertEquals($this->directory->getAccessTime(), time());

        $this->directory->setAccessTime(time() - 1000);
        self::assertEquals($this->directory->getAccessTime(), time() - 1000);

        $this->directory->delete();
    }


    /**
     * Testing clearing a directory
     * @return void
     * @throws DirectoryException
     */
    public function testClear(): void
    {
        $fileCollection = new Collection();
        $fileCollection->append(new File($this->directory->getPath() . 'test.txt'));

        $this->directory->create();
        touch($this->directory->getPath() . 'test.txt');
        self::assertEquals($fileCollection, $this->directory->getContent());
        $this->directory->flush();
        self::assertEquals(new Collection(), $this->directory->getContent());

        $this->directory->delete();
    }


    /**
     * Testing changing owner id of a directory
     * @return void
     */
    public function testChmod(): void
    {
        $this->directory->create();
        self::assertTrue($this->directory->chmod($this->directory->getOwnerId()));
        $this->directory->delete();
    }


    /**
     * Testing changing the owner of a directory
     * @return void
     */
    public function testChangingOwner(): void
    {
        $this->directory->create();

        $this->directory->setOwnerId($this->directory->getOwnerId());
        self::assertEquals($this->directory->getOwnerId(), $this->directory->getOwnerId());

        $this->directory->setOwner($this->directory->getOwner());
        self::assertEquals($this->directory->getOwner(), $this->directory->getOwner());

        $this->directory->delete();
    }

    /**
     * Testing changing the group of a directory
     * @return void
     */
    public function testChangingGroup(): void
    {
        $this->directory->create();

        $this->directory->setGroupId($this->directory->getGroupId());
        self::assertEquals($this->directory->getGroupId(), $this->directory->getGroupId());

        $this->directory->delete();
    }

    /**
     * Testing retrieving image dimensions
     * @return void
     */
    public function testCopy(): void
    {
        $directory = new Directory($this->path . 'copy');
        $directory->create();

        $this->directory->create();
        touch($this->directory->getPath() . 'test.txt');

        $this->directory->copy($directory);
        self::assertTrue(is_file($directory->getPath() . 'test.txt'));

        $this->directory->delete();
        $directory->delete();
    }
}