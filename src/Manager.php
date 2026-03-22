<?php

declare(strict_types=1);

namespace AndrewDyer\Settings;

use AndrewDyer\Settings\Exceptions\MissingSettingException;

readonly class Manager implements SettingsInterface
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
        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }

        return $this->getNestedValue($key);
    }

    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->settings)) {
            return true;
        }

        return $this->hasNestedKey($key);
    }

    private function getNestedValue(string $key): mixed
    {
        $keys = explode('.', $key);
        $array = $this->settings;

        foreach ($keys as $k) {
            if (!array_key_exists($k, $array)) {
                throw MissingSettingException::forKey($key);
            }

            $array = $array[$k];
        }

        return $array;
    }

    private function hasNestedKey(string $key): bool
    {
        $keys = explode('.', $key);
        $array = $this->settings;
        $lastIndex = count($keys) - 1;

        foreach ($keys as $index => $k) {
            if (!array_key_exists($k, $array)) {
                return false;
            }

            $array = $array[$k];

            if (!is_array($array) && $index < $lastIndex) {
                return false;
            }
        }

        return true;
    }
}
