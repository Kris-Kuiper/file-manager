<?php

declare(strict_types=1);

use KrisKuiper\FileManager\Exceptions\DirectoryException;
use KrisKuiper\FileManager\Exceptions\FileException;
use PHPUnit\Framework\TestCase;
use KrisKuiper\FileManager\File;
use KrisKuiper\FileManager\Directory;

final class FileTest extends TestCase
{
    /**
     * The path to the assets folder
     */
    private ?string $path = null;
    private File $file;

    public function setUp(): void {

        $this->path = getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'file'. DIRECTORY_SEPARATOR;
        $this->file = new File($this->path . 'file.txt');
        $this->__destruct();
    }

    /**
     * Destructor
     */
    public function __destruct() {
        @unlink($this->path . 'file.txt');
    }

    /**
     * Testing creating a new file
     * Testing if file is readable
     * Testing if file is writable
     * Testing if file exists
     */
    public function testCreatingFile(): void {

        self::assertFalse($this->file->isReadable());
        self::assertFalse($this->file->isWritable());
        self::assertFalse($this->file->exists());

        $this->file->create();
        $this->file->setOwner('administrator');
        $this->file->setGroupId(1000);

        self::assertTrue($this->file->isReadable());
        self::assertTrue($this->file->isWritable());
        self::assertTrue($this->file->exists());

        $this->file->delete();
    }

    /**
     * Testing changing the base name of a file
     * @return void
     */
    public function testChangingBaseName(): void {

        $this->file->create();
        $this->file->setBaseName('new-name.txt');
        self::assertEquals('new-name.txt', $this->file->getBaseName());
        self::assertFileExists($this->path . 'new-name.txt');

        $this->file->delete();
    }

    /**
     * Testing changing the name of a file
     * @return void
     */
    public function testChangingName(): void {

        $this->file->create();
        $this->file->setName('new-name');

        self::assertEquals('new-name', $this->file->getName());
        self::assertFileExists($this->path . 'new-name.txt');

        $this->file->delete();
    }

    /**
     * Testing the mime type of a file
     * @return void
     */
    public function testMime(): void {

        $this->file->create();
        self::assertEquals('text/plain', $this->file->getMimeType());
        $this->file->delete();
    }

    /**
     * Testing changing the parent path of a file (moving the file)
     * @throws DirectoryException
     */
    public function testChangingBasePath(): void {

        $this->file->create();
        $this->file->setBasePath(new Directory($this->path));

        self::assertEquals($this->file->getBasePath(), $this->path);
        self::assertFileExists($this->path . 'file.txt');

        $this->file->delete();
    }

    /**
     * Testing changing the extension of a file
     * @return void
     */
    public function testChangingExtension(): void {

        $this->file->create();
        $this->file->setExtension('.tmp');
        self::assertEquals('tmp', $this->file->getExtension());
        self::assertFileExists($this->path . 'file.tmp');
        $this->file->delete();

        $this->file->create();
        $this->file->setExtension('tmp');
        self::assertEquals('tmp', $this->file->getExtension());
        self::assertFileExists($this->path . 'file.tmp');
        $this->file->delete();
    }

    /**
     * Testing changing the extension of a file
     * Testing appending text to a file
     * @throws FileException
     */
    public function testFileSize(): void {

        $this->file->create();
        $this->file->append('test');
        self::assertEquals(4, $this->file->getFileSize());
        self::assertEquals('test', $this->file->getContent());
        $this->file->delete();
    }

    /**
     * Testing changing the modification time of a file
     * @return void
     */
    public function testModificationTime(): void {

        $this->file->create();
        self::assertEquals($this->file->getModificationTime(), time());

        $this->file->setModificationTime(time() - 1000);
        self::assertEquals($this->file->getModificationTime(), time() - 1000);

        $this->file->delete();
    }

    /**
     * Testing changing the access time of a file
     * @return void
     */
    public function testAccessTime(): void {

        $this->file->create();
        self::assertEquals($this->file->getAccessTime(), time());

        $this->file->setAccessTime(time() - 1000);
        self::assertEquals($this->file->getAccessTime(), time() - 1000);

        $this->file->delete();
    }

    /**
     * Testing changing the owner of a file
     * @return void
     */
    public function testChangingOwner(): void {

        $this->file->create();

        $this->file->setOwnerId($this->file->getOwnerId());
        self::assertEquals($this->file->getOwnerId(), $this->file->getOwnerId());

        $this->file->setOwner($this->file->getOwner());
        self::assertEquals($this->file->getOwner(), $this->file->getOwner());

        $this->file->delete();
    }

    /**
     * Testing changing the group of a file
     * @return void
     */
    public function testChangingGroup(): void {

        $this->file->create();

        $this->file->setGroupId($this->file->getGroupId());
        self::assertEquals($this->file->getGroupId(), $this->file->getGroupId());

        $this->file->delete();
    }

    /**
     * Testing retrieving image dimensions
     * @return void
     */
    public function testRetrievingDimensions(): void {

        $file = new File($this->path . 'image.png');
        self::assertEquals(120, $file->getWidth());
        self::assertEquals(120, $file->getHeight());
    }

    /**
     * Testing retrieving image dimensions
     * @throws DirectoryException
     */
    public function testCopy(): void {

        $this->file->create();
        $this->file->copy(new Directory($this->path), 'copy.txt');
        self::assertFileExists($this->path . 'copy.txt');

        $file = new File($this->path . 'copy.txt');
        $file->delete();
        $this->file->delete();
    }

    /**
     * Testing append text to a file
     * Testing prepend text to a file
     * Testing flushing text to a file
     * @throws FileException
     */
    public function testSettingText(): void {

        $this->file->create();
        $this->file->append('World!');
        $this->file->prepend('Hello ');
        self::assertEquals('Hello World!', $this->file->getContent());
        $this->file->flush();
        self::assertEquals(null, $this->file->getContent());
        $this->file->delete();
    }
}