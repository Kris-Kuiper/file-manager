<?php
declare(strict_types=1);

namespace KrisKuiper\FileManager\Exceptions;

use Exception;

class CameraInfoException extends Exception
{
    public static function exifNotLoaded(): self
    {
        return new self('Extension exif not loaded');
    }
}