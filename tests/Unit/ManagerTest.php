<?php

declare(strict_types=1);

namespace AndrewDyer\Settings\Tests\Unit;

use AndrewDyer\Settings\Exceptions\MissingSettingException;
use AndrewDyer\Settings\Manager;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    public function testAllReturnsAllSettings(): void
    {
        $settings = new Manager([
            'timezone' => 'UTC',
            'locale' => 'en_US',
        ]);

        self::assertCount(2, $settings->all());
    }

    public function testHasReturnsTrueWhenKeyExists(): void
    {
        $settings = new Manager([
            'timezone' => 'UTC',
        ]);

        self::assertTrue($settings->has('timezone'));
    }

    public function testHasReturnsFalseWhenKeyDoesNotExist(): void
    {
        $settings = new Manager([
            'timezone' => 'UTC',
        ]);

        self::assertFalse($settings->has('unknown'));
    }

    public function testHasReturnsTrueForNestedKey(): void
    {
        $settings = new Manager([
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
        $settings = new Manager([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        self::assertFalse($settings->has('database.port'));
    }

    public function testHasReturnsFalseWhenIntermediateKeyDoesNotExist(): void
    {
        $settings = new Manager([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        self::assertFalse($settings->has('cache.driver'));
    }

    public function testHasReturnsTrueForDeepNestedKey(): void
    {
        $settings = new Manager([
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

    public function testHasReturnsTrueForLiteralKeyContainingDot(): void
    {
        $settings = new Manager([
            'database.host' => 'localhost',
        ]);

        self::assertTrue($settings->has('database.host'));
    }

    public function testHasPrefersLiteralDotKeyOverNested(): void
    {
        $settings = new Manager([
            'database.host' => 'literal',
            'database' => [
                'host' => 'nested',
            ],
        ]);

        self::assertTrue($settings->has('database.host'));
    }

    public function testGetReturnsSettingByKey(): void
    {
        $settings = new Manager([
            'timezone' => 'UTC',
        ]);

        self::assertSame('UTC', $settings->get('timezone'));
    }

    public function testGetReturnsNullWhenKeyExistsWithNullValue(): void
    {
        $settings = new Manager([
            'timezone' => null,
        ]);

        self::assertNull($settings->get('timezone'));
    }

    public function testGetWithNumericStringKeyReturnsCorrectValue(): void
    {
        $settings = new Manager([
            '0' => 'zero',
        ]);

        self::assertSame('zero', $settings->get('0'));
    }

    public function testGetThrowsMissingSettingExceptionWhenKeyNotFound(): void
    {
        $settings = new Manager([
            'timezone' => 'UTC',
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "unknown".');

        $settings->get('unknown');
    }

    public function testGetReturnsNestedValueWithDotNotation(): void
    {
        $settings = new Manager([
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
        $settings = new Manager([
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
        $settings = new Manager([
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
        $settings = new Manager([
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
        $settings = new Manager([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "cache.driver".');

        $settings->get('cache.driver');
    }

    public function testGetReturnsNullForNestedKeyWithNullValue(): void
    {
        $settings = new Manager([
            'database' => [
                'password' => null,
            ],
        ]);

        self::assertNull($settings->get('database.password'));
    }

    public function testGetReturnsLiteralKeyContainingDot(): void
    {
        $settings = new Manager([
            'database.host' => 'localhost',
        ]);

        self::assertSame('localhost', $settings->get('database.host'));
    }

    public function testGetPrefersLiteralDotKeyOverNestedValue(): void
    {
        $settings = new Manager([
            'database.host' => 'literal',
            'database' => [
                'host' => 'nested',
            ],
        ]);

        self::assertSame('literal', $settings->get('database.host'));
    }
}
