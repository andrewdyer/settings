<?php

declare(strict_types=1);

namespace Anddye\Settings;

use Anddye\Settings\Exceptions\MissingSettingException;

readonly class Settings implements SettingsInterface
{
    public function __construct(private array $settings)
    {
    }

    public function all(): array
    {
        return $this->settings;
    }

    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            throw MissingSettingException::forKey($key);
        }

        return $this->getNestedValue($key);
    }

    public function has(string $key): bool
    {
        return $this->checkNestedKey($key);
    }

    private function getNestedValue(string $key): mixed
    {
        $keys = explode('.', $key);
        $array = $this->settings;

        foreach ($keys as $k) {
            if (!array_key_exists($k, $array)) {
                return null;
            }

            $array = $array[$k];
        }

        return $array;
    }

    private function checkNestedKey(string $key): bool
    {
        $keys = explode('.', $key);
        $array = $this->settings;

        foreach ($keys as $k) {
            if (!array_key_exists($k, $array)) {
                return false;
            }

            $array = $array[$k];

            if (!is_array($array) && $k !== end($keys)) {
                return false;
            }
        }

        return true;
    }
}
