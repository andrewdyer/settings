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
        $settings = new Settings([
            'timezone' => 'UTC',
            'locale' => 'en_US',
        ]);

        self::assertCount(2, $settings->all());
    }

    public function testGetReturnsSettingByKey(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
        ]);

        self::assertSame('UTC', $settings->get('timezone'));
    }

    public function testGetReturnsNullWhenKeyExistsWithNullValue(): void
    {
        $settings = new Settings([
            'timezone' => null,
        ]);

        self::assertNull($settings->get('timezone'));
    }

    public function testGetWithNumericStringKeyReturnsCorrectValue(): void
    {
        $settings = new Settings([
            '0' => 'zero',
        ]);

        self::assertSame('zero', $settings->get('0'));
    }

    public function testGetThrowsMissingSettingExceptionWhenKeyNotFound(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "unknown".');

        $settings->get('unknown');
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

    public function testGetReturnsNestedValueWithDotNotation(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
                'port' => 5432,
            ],
        ]);

        self::assertSame('localhost', $settings->get('database.host'));
        self::assertSame(5432, $settings->get('database.port'));
    }

    public function testGetReturnsNestedArrayValue(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
                'port' => 5432,
            ],
        ]);

        $expected = [
            'host' => 'localhost',
            'port' => 5432,
        ];
        self::assertSame($expected, $settings->get('database'));
    }

    public function testGetReturnsDeepNestedValue(): void
    {
        $settings = new Settings([
            'app' => [
                'services' => [
                    'cache' => [
                        'driver' => 'redis',
                    ],
                ],
            ],
        ]);

        self::assertSame('redis', $settings->get('app.services.cache.driver'));
    }

    public function testGetThrowsExceptionForMissingNestedKey(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "database.port".');

        $settings->get('database.port');
    }

    public function testGetThrowsExceptionWhenIntermediateKeyDoesNotExist(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "cache.driver".');

        $settings->get('cache.driver');
    }

    public function testHasReturnsTrueForNestedKey(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
                'port' => 5432,
            ],
        ]);

        self::assertTrue($settings->has('database.host'));
        self::assertTrue($settings->has('database.port'));
    }

    public function testHasReturnsFalseForMissingNestedKey(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        self::assertFalse($settings->has('database.port'));
    }

    public function testHasReturnsFalseWhenIntermediateKeyDoesNotExist(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        self::assertFalse($settings->has('cache.driver'));
    }

    public function testHasReturnsTrueForDeepNestedKey(): void
    {
        $settings = new Settings([
            'app' => [
                'services' => [
                    'cache' => [
                        'driver' => 'redis',
                    ],
                ],
            ],
        ]);

        self::assertTrue($settings->has('app.services.cache.driver'));
    }

    public function testGetReturnsNullForNestedKeyWithNullValue(): void
    {
        $settings = new Settings([
            'database' => [
                'password' => null,
            ],
        ]);

        self::assertNull($settings->get('database.password'));
    }

    public function testGetReturnsLiteralKeyContainingDot(): void
    {
        $settings = new Settings([
            'database.host' => 'localhost',
        ]);

        self::assertSame('localhost', $settings->get('database.host'));
    }

    public function testGetPrefersLiteralDotKeyOverNestedValue(): void
    {
        $settings = new Settings([
            'database.host' => 'literal',
            'database' => [
                'host' => 'nested',
            ],
        ]);

        self::assertSame('literal', $settings->get('database.host'));
    }

    public function testHasReturnsTrueForLiteralKeyContainingDot(): void
    {
        $settings = new Settings([
            'database.host' => 'localhost',
        ]);

        self::assertTrue($settings->has('database.host'));
    }

    public function testHasPrefersLiteralDotKeyOverNested(): void
    {
        $settings = new Settings([
            'database.host' => 'literal',
            'database' => [
                'host' => 'nested',
            ],
        ]);

        self::assertTrue($settings->has('database.host'));
    }
}
