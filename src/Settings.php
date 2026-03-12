<?php

declare(strict_types=1);

namespace Anddye\Settings;

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

    public function get(string $key = '')
    {
        return ($key === '') ? $this->settings : $this->settings[$key];
    }
}
