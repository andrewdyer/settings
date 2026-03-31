<?php

declare(strict_types=1);

namespace AndrewDyer\Settings\Contracts;

use AndrewDyer\Settings\Exceptions\MissingSettingException;

/**
 * Defines read-only access to application configuration values.
 */
interface SettingsInterface
{
    /**
     * Returns all available settings as a flat or nested array.
     *
     * @return array<mixed> Complete settings payload.
     */
    public function all(): array;

    /**
     * Returns the value for a setting key.
     *
     * Supports direct keys and dot notation for nested arrays.
     *
     * @param string $key Setting key to resolve.
     *
     * @return mixed Resolved setting value.
     *
     * @throws MissingSettingException When the key cannot be resolved.
     */
    public function get(string $key): mixed;

    /**
     * Determines whether a setting key exists.
     *
     * Supports direct keys and dot notation for nested arrays.
     *
     * @param string $key Setting key to check.
     *
     * @return bool True when the key exists; otherwise false.
     */
    public function has(string $key): bool;
}
