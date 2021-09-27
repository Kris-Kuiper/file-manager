<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager;

use KrisKuiper\FileManager\Contracts\FileInterface;
use KrisKuiper\FileManager\Exceptions\CameraInfoException;

class CameraInfo
{
    private array $headers;

    /**
     * @throws CameraInfoException
     */
    public function __construct(FileInterface $file)
    {
        if(false === extension_loaded('exif')) {
            throw CameraInfoException::exifNotLoaded();
        }

        $this->headers = @exif_read_data($file->getPath()) ?? [];
    }

    public function getMake(): ?string
    {
        return $this->headers['Make'] ?? null;
    }

    public function getModel(): ?string
    {
        return $this->headers['Model'] ?? null;
    }

    public function getOrientation(): ?string
    {
        return $this->headers['Orientation'] ?? null;
    }

    public function getXResolution(): ?string
    {
        return $this->headers['XResolution'] ?? null;
    }

    public function getYResolution(): ?string
    {
        return $this->headers['YResolution'] ?? null;
    }

    public function getResolutionUnit(): ?string
    {
        return $this->headers['ResolutionUnit'] ?? null;
    }

    public function getSoftware(): ?string
    {
        return $this->headers['Software'] ?? null;
    }

    public function getExposureTime(): ?string
    {
        return $this->headers['ExposureTime'] ?? null;
    }

    public function getFNumber(): ?string
    {
        return $this->headers['FNumber'] ?? null;
    }

    public function getISOSpeedRatings(): ?string
    {
        return $this->headers['ISOSpeedRatings'] ?? null;
    }

    public function getShutterSpeed(): ?string
    {
        return $this->headers['ShutterSpeedValue'] ?? null;
    }

    public function getAperture(): ?string
    {
        return $this->headers['ApertureValue'] ?? null;
    }

    public function getBrightness(): ?string
    {
        return $this->headers['BrightnessValue'] ?? null;
    }

    public function getExposureBias(): ?string
    {
        return $this->headers['ExposureBiasValue'] ?? null;
    }

    public function getMaxAperture(): ?string
    {
        return $this->headers['MaxApertureValue'] ?? null;
    }

    public function getMeteringMode(): ?string
    {
        return $this->headers['MeteringMode'] ?? null;
    }

    public function getFlash(): ?string
    {
        return $this->headers['Flash'] ?? null;
    }

    public function getCreatedAt(): ?string
    {
        return $this->headers['DateTime'] ?? null;
    }

    public function getMimeType(): ?string
    {
        return $this->headers['MimeType'] ?? null;
    }
}