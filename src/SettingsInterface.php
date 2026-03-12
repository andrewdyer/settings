<?php

declare(strict_types=1);

namespace Anddye\Settings;

interface SettingsInterface
{
    public function get(string $key = '');
}
