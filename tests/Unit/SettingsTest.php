<?php

declare(strict_types=1);

namespace Anddye\Settings\Tests\Unit;

use Anddye\Settings\Exceptions\MissingSettingException;
use Anddye\Settings\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public function testAllReturnsAllSettings(): void
    {
        $settings = [
            'timezone' => 'UTC',
            'locale' => 'en_US',
        ];

        $instance = new Settings($settings);

        self::assertSame($settings, $instance->all());
    }

    public function testGetReturnsSettingByKey(): void
    {
        $instance = new Settings([
            'timezone' => 'UTC',
        ]);

        self::assertSame('UTC', $instance->get('timezone'));
    }

    public function testGetReturnsNullWhenKeyExistsWithNullValue(): void
    {
        $instance = new Settings([
            'timezone' => null,
        ]);

        self::assertNull($instance->get('timezone'));
    }

    public function testGetWithNumericStringKeyReturnsCorrectValue(): void
    {
        $instance = new Settings([
            '0' => 'zero',
        ]);

        self::assertSame('zero', $instance->get('0'));
    }

    public function testGetThrowsMissingSettingExceptionWhenKeyNotFound(): void
    {
        $instance = new Settings([
            'timezone' => 'UTC'
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "unknown".');

        $instance->get('unknown');
    }

    public function testHasReturnsTrueWhenKeyExists(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
        ]);

        self::assertTrue($settings->has('timezone'));
    }

    public function testHasReturnsFalseWhenKeyDoesNotExist(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
        ]);

        self::assertFalse($settings->has('unknown'));
    }
}
