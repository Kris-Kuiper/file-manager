<?php
declare(strict_types=1);

namespace KrisKuiper\FileManager\Exceptions;

use Exception;

class DirectoryException extends Exception
{
    public static function directoryNotExists(string $directory): self
    {
        return new self(sprintf('Directory "%s" does not exists', $directory));
    }

    public static function directoryNotWritable(string $directory): self
    {
        return new self(sprintf('Directory "%s" is not writable', $directory));
    }
}