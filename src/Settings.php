<?php

declare(strict_types=1);

namespace Anddye\Settings;

use Anddye\Settings\Exceptions\MissingSettingException;

class Settings implements SettingsInterface
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

        return $this->settings[$key];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->settings);
    }
}
