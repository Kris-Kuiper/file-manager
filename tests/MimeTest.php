<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use KrisKuiper\FileManager\MimeType;

final class MimeTest extends TestCase
{
    public function testCorrectValueWhenSettingAndRetrievingBasedOnKey(): void
    {
        self::assertNull(MimeType::get('key'));
        MimeType::set('key', 'value');
        self::assertEquals('value', MimeType::get('key'));
    }

    public function testIfKeyExistsWhenSettingKeyWithValue(): void
    {
        MimeType::set('key', 'value');
        self::assertTrue(MimeType::exists('key'));
        self::assertFalse(MimeType::exists('foo'));
    }
}