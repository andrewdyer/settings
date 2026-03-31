<?php

declare(strict_types=1);

namespace AndrewDyer\Settings;

use AndrewDyer\Settings\Contracts\SettingsInterface;
use AndrewDyer\Settings\Exceptions\MissingSettingException;

/**
 * Provides read-only access to configuration values by key.
 *
 * Supports both literal keys and nested lookup via dot notation.
 */
readonly class Settings implements SettingsInterface
{
    /**
     * Internal settings storage used for direct and nested lookups.
     *
     * @var array<mixed>
     */
    private array $settings;

    /**
     * Creates a new settings collection.
     *
     * @param array<mixed> $settings Settings payload indexed by key.
     *
     * @return void Instance initialization result.
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Returns all configured settings.
     *
     * @return array<mixed> Complete settings payload.
     */
    public function all(): array
    {
        return $this->settings;
    }

    /**
     * Returns the value for a setting key.
     *
     * Direct keys are resolved first, then dot notation is attempted.
     *
     * @param string $key Setting key to resolve.
     *
     * @return mixed Resolved setting value.
     *
     * @throws MissingSettingException When the key cannot be resolved.
     */
    public function get(string $key): mixed
    {
        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }

        return $this->getNestedValue($key);
    }

    /**
     * Determines whether a setting key exists.
     *
     * Direct keys are checked first, then dot notation is attempted.
     *
     * @param string $key Setting key to check.
     *
     * @return bool True when the key exists; otherwise false.
     */
    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->settings)) {
            return true;
        }

        return $this->hasNestedKey($key);
    }

    /**
     * Resolves a nested setting value using dot notation.
     *
     * @param string $key Dot-notated key path.
     *
     * @return mixed Resolved nested value.
     *
     * @throws MissingSettingException When any segment in the path is missing.
     */
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

    /**
     * Determines whether a nested key path exists.
     *
     * @param string $key Dot-notated key path.
     *
     * @return bool True when all path segments exist; otherwise false.
     */
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
