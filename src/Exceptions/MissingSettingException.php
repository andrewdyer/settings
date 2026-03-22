<?php

declare(strict_types=1);

namespace AndrewDyer\Settings\Exceptions;

use RuntimeException;

class MissingSettingException extends RuntimeException
{
    public static function forKey(string $key): self
    {
        return new self(sprintf('Setting not found for key "%s".', $key));
    }
}
