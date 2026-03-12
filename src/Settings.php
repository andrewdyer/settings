<?php

declare(strict_types=1);

namespace Anddye\Settings;

use Anddye\Settings\Exceptions\MissingSettingException;

class Settings implements SettingsInterface
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function all(): array
    {
        return $this->settings;
    }

    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->settings)) {
            throw MissingSettingException::forKey($key);
        }

        return $this->settings[$key];
    }
}
