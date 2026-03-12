<?php

declare(strict_types=1);

namespace Anddye\Settings\Tests\Unit;

use Anddye\Settings\Settings;
use ErrorException;
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

    public function testGetReturnsAllSettingsWhenKeyIsEmpty(): void
    {
        $settings = [
            'app_name' => 'php-settings',
            'debug' => true,
            'retries' => 3,
        ];

        $instance = new Settings($settings);

        self::assertSame($settings, $instance->get());
    }

    public function testGetReturnsSettingByKey(): void
    {
        $settings = [
            'timezone' => 'UTC',
            'locale' => 'en_US',
        ];

        $instance = new Settings($settings);

        self::assertSame('UTC', $instance->get('timezone'));
    }

    public function testGetWithNumericStringKeyReturnsCorrectValue(): void
    {
        $settings = [
            '0' => 'zero',
            '1' => 'one',
        ];

        $instance = new Settings($settings);

        self::assertSame('zero', $instance->get('0'));
    }

    public function testGetWithMissingKeyThrowsErrorExceptionWhenWarningsAreConvertedToExceptions(): void
    {
        $instance = new Settings(['known' => 'value']);

        set_error_handler(
            static function(int $severity, string $message, string $file, int $line): never {
                throw new ErrorException($message, 0, $severity, $file, $line);
            }
        );

        try {
            $this->expectException(ErrorException::class);
            $this->expectExceptionMessage('Undefined array key "unknown"');

            $instance->get('unknown');
        } finally {
            restore_error_handler();
        }
    }
}
