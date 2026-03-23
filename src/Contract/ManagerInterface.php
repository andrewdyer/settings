<?php

declare(strict_types=1);

namespace AndrewDyer\Settings\Contract;

interface ManagerInterface
{
    public function all(): array;

    public function get(string $key): mixed;

    public function has(string $key): bool;
}
