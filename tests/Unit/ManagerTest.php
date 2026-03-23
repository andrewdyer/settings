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
        $manager = new Manager([
            'timezone' => 'UTC',
            'locale' => 'en_US',
        ]);

        self::assertCount(2, $manager->all());
    }

    public function testHasReturnsTrueWhenKeyExists(): void
    {
        $manager = new Manager([
            'timezone' => 'UTC',
        ]);

        self::assertTrue($manager->has('timezone'));
    }

    public function testHasReturnsFalseWhenKeyDoesNotExist(): void
    {
        $manager = new Manager([
            'timezone' => 'UTC',
        ]);

        self::assertFalse($manager->has('unknown'));
    }

    public function testHasReturnsTrueForNestedKey(): void
    {
        $manager = new Manager([
            'database' => [
                'host' => 'localhost',
                'port' => 5432,
            ],
        ]);

        self::assertTrue($manager->has('database.host'));
        self::assertTrue($manager->has('database.port'));
    }

    public function testHasReturnsFalseForMissingNestedKey(): void
    {
        $manager = new Manager([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        self::assertFalse($manager->has('database.port'));
    }

    public function testHasReturnsFalseWhenIntermediateKeyDoesNotExist(): void
    {
        $manager = new Manager([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        self::assertFalse($manager->has('cache.driver'));
    }

    public function testHasReturnsTrueForDeepNestedKey(): void
    {
        $manager = new Manager([
            'app' => [
                'services' => [
                    'cache' => [
                        'driver' => 'redis',
                    ],
                ],
            ],
        ]);

        self::assertTrue($manager->has('app.services.cache.driver'));
    }

    public function testHasReturnsTrueForLiteralKeyContainingDot(): void
    {
        $manager = new Manager([
            'database.host' => 'localhost',
        ]);

        self::assertTrue($manager->has('database.host'));
    }

    public function testHasPrefersLiteralDotKeyOverNested(): void
    {
        $manager = new Manager([
            'database.host' => 'literal',
            'database' => [
                'host' => 'nested',
            ],
        ]);

        self::assertTrue($manager->has('database.host'));
    }

    public function testGetReturnsSettingByKey(): void
    {
        $manager = new Manager([
            'timezone' => 'UTC',
        ]);

        self::assertSame('UTC', $manager->get('timezone'));
    }

    public function testGetReturnsNullWhenKeyExistsWithNullValue(): void
    {
        $manager = new Manager([
            'timezone' => null,
        ]);

        self::assertNull($manager->get('timezone'));
    }

    public function testGetWithNumericStringKeyReturnsCorrectValue(): void
    {
        $manager = new Manager([
            '0' => 'zero',
        ]);

        self::assertSame('zero', $manager->get('0'));
    }

    public function testGetThrowsMissingSettingExceptionWhenKeyNotFound(): void
    {
        $manager = new Manager([
            'timezone' => 'UTC',
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "unknown".');

        $manager->get('unknown');
    }

    public function testGetReturnsNestedValueWithDotNotation(): void
    {
        $manager = new Manager([
            'database' => [
                'host' => 'localhost',
                'port' => 5432,
            ],
        ]);

        self::assertSame('localhost', $manager->get('database.host'));
        self::assertSame(5432, $manager->get('database.port'));
    }

    public function testGetReturnsNestedArrayValue(): void
    {
        $manager = new Manager([
            'database' => [
                'host' => 'localhost',
                'port' => 5432,
            ],
        ]);

        $expected = [
            'host' => 'localhost',
            'port' => 5432,
        ];
        self::assertSame($expected, $manager->get('database'));
    }

    public function testGetReturnsDeepNestedValue(): void
    {
        $manager = new Manager([
            'app' => [
                'services' => [
                    'cache' => [
                        'driver' => 'redis',
                    ],
                ],
            ],
        ]);

        self::assertSame('redis', $manager->get('app.services.cache.driver'));
    }

    public function testGetThrowsExceptionForMissingNestedKey(): void
    {
        $manager = new Manager([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "database.port".');

        $manager->get('database.port');
    }

    public function testGetThrowsExceptionWhenIntermediateKeyDoesNotExist(): void
    {
        $manager = new Manager([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "cache.driver".');

        $manager->get('cache.driver');
    }

    public function testGetReturnsNullForNestedKeyWithNullValue(): void
    {
        $manager = new Manager([
            'database' => [
                'password' => null,
            ],
        ]);

        self::assertNull($manager->get('database.password'));
    }

    public function testGetReturnsLiteralKeyContainingDot(): void
    {
        $manager = new Manager([
            'database.host' => 'localhost',
        ]);

        self::assertSame('localhost', $manager->get('database.host'));
    }

    public function testGetPrefersLiteralDotKeyOverNestedValue(): void
    {
        $manager = new Manager([
            'database.host' => 'literal',
            'database' => [
                'host' => 'nested',
            ],
        ]);

        self::assertSame('literal', $manager->get('database.host'));
    }
}
