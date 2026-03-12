<?php

declare(strict_types=1);

namespace Anddye\Settings;

interface SettingsInterface
{
    public function all(): array;

    public function get(string $key): mixed;
}
