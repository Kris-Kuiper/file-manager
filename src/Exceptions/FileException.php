<?php
declare(strict_types=1);

namespace KrisKuiper\FileManager\Exceptions;

use Exception;

class FileException extends Exception
{
    public static function fileNotReadable(string $file): self
    {
        return new self(sprintf('File "%s" is not readable', $file));
    }

    public static function fileNotWritable(string $file): self
    {
        return new self(sprintf('File "%s" is not writable', $file));
    }
}