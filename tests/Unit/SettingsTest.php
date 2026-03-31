<?php

declare(strict_types=1);

namespace AndrewDyer\Settings\Tests\Unit;

use AndrewDyer\Settings\Exceptions\MissingSettingException;
use AndrewDyer\Settings\Settings;
use PHPUnit\Framework\TestCase;

/**
 * Verifies behavior of the settings repository for direct and nested keys.
 */
class SettingsTest extends TestCase
{
    /**
     * Verifies that all configured settings are returned.
     *
     * @return void Test execution result.
     */
    public function testAllReturnsAllSettings(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
            'locale' => 'en_US',
        ]);

        self::assertCount(2, $settings->all());
    }

    /**
     * Verifies that has returns true for an existing direct key.
     *
     * @return void Test execution result.
     */
    public function testHasReturnsTrueWhenKeyExists(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
        ]);

        self::assertTrue($settings->has('timezone'));
    }

    /**
     * Verifies that has returns false for a missing direct key.
     *
     * @return void Test execution result.
     */
    public function testHasReturnsFalseWhenKeyDoesNotExist(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
        ]);

        self::assertFalse($settings->has('unknown'));
    }

    /**
     * Verifies that has resolves existing nested keys via dot notation.
     *
     * @return void Test execution result.
     */
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

    /**
     * Verifies that has returns false for a missing nested key segment.
     *
     * @return void Test execution result.
     */
    public function testHasReturnsFalseForMissingNestedKey(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        self::assertFalse($settings->has('database.port'));
    }

    /**
     * Verifies that has returns false when an intermediate path key is absent.
     *
     * @return void Test execution result.
     */
    public function testHasReturnsFalseWhenIntermediateKeyDoesNotExist(): void
    {
        $settings = new Settings([
            'database' => [
                'host' => 'localhost',
            ],
        ]);

        self::assertFalse($settings->has('cache.driver'));
    }

    /**
     * Verifies that has supports deep nested path lookups.
     *
     * @return void Test execution result.
     */
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

    /**
     * Verifies that has supports literal keys that contain dots.
     *
     * @return void Test execution result.
     */
    public function testHasReturnsTrueForLiteralKeyContainingDot(): void
    {
        $settings = new Settings([
            'database.host' => 'localhost',
        ]);

        self::assertTrue($settings->has('database.host'));
    }

    /**
     * Verifies that has prefers a matching literal dot key over nested lookup.
     *
     * @return void Test execution result.
     */
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

    /**
     * Verifies that get returns a direct key value.
     *
     * @return void Test execution result.
     */
    public function testGetReturnsSettingByKey(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
        ]);

        self::assertSame('UTC', $settings->get('timezone'));
    }

    /**
     * Verifies that get returns null when a direct key exists with null value.
     *
     * @return void Test execution result.
     */
    public function testGetReturnsNullWhenKeyExistsWithNullValue(): void
    {
        $settings = new Settings([
            'timezone' => null,
        ]);

        self::assertNull($settings->get('timezone'));
    }

    /**
     * Verifies that get resolves numeric-string keys correctly.
     *
     * @return void Test execution result.
     */
    public function testGetWithNumericStringKeyReturnsCorrectValue(): void
    {
        $settings = new Settings([
            '0' => 'zero',
        ]);

        self::assertSame('zero', $settings->get('0'));
    }

    /**
     * Verifies that get throws when a direct key does not exist.
     *
     * @return void Test execution result.
     */
    public function testGetThrowsMissingSettingExceptionWhenKeyNotFound(): void
    {
        $settings = new Settings([
            'timezone' => 'UTC',
        ]);

        $this->expectException(MissingSettingException::class);
        $this->expectExceptionMessage('Setting not found for key "unknown".');

        $settings->get('unknown');
    }

    /**
     * Verifies that get resolves nested values via dot notation.
     *
     * @return void Test execution result.
     */
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

    /**
     * Verifies that get can return a nested array value.
     *
     * @return void Test execution result.
     */
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

    /**
     * Verifies that get supports deep nested path lookups.
     *
     * @return void Test execution result.
     */
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

    /**
     * Verifies that get throws when a nested terminal key is missing.
     *
     * @return void Test execution result.
     */
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

    /**
     * Verifies that get throws when an intermediate nested key is missing.
     *
     * @return void Test execution result.
     */
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

    /**
     * Verifies that get returns null for nested keys with null values.
     *
     * @return void Test execution result.
     */
    public function testGetReturnsNullForNestedKeyWithNullValue(): void
    {
        $settings = new Settings([
            'database' => [
                'password' => null,
            ],
        ]);

        self::assertNull($settings->get('database.password'));
    }

    /**
     * Verifies that get supports literal keys that contain dots.
     *
     * @return void Test execution result.
     */
    public function testGetReturnsLiteralKeyContainingDot(): void
    {
        $settings = new Settings([
            'database.host' => 'localhost',
        ]);

        self::assertSame('localhost', $settings->get('database.host'));
    }

    /**
     * Verifies that get prefers a matching literal dot key over nested lookup.
     *
     * @return void Test execution result.
     */
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
}
