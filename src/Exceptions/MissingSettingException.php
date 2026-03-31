<?php

declare(strict_types=1);

namespace AndrewDyer\Settings\Exceptions;

use RuntimeException;

/**
 * Thrown when a requested setting key cannot be resolved.
 */
class MissingSettingException extends RuntimeException
{
    /**
     * Creates an exception instance for a missing key.
     *
     * @param string $key Missing setting key.
     *
     * @return self Exception describing the missing key.
     */
    public static function forKey(string $key): self
    {
        return new self(sprintf('Setting not found for key "%s".', $key));
    }
}
