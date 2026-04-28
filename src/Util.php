<?php

namespace Cascade;

/**
 * Class Util
 *
 * @package Cascade
 */
class Util
{
    /**
     * Convert a string from snake_case to camelCase.
     *
     * If the input is not a string, null is returned.
     *
     * @param string $input Input snake_case string
     * @return null|string Output camelCase string
     */
    public static function snakeToCamelCase($input): ?string
    {
        if (!is_string($input)) {
            return null;
        }

        $output = preg_replace_callback('/(^|_)+(.)/', fn($match): string => strtoupper($match[2]), $input);

        return lcfirst((string) $output);
    }
}
