<?php

namespace App\Traits;

/**
 * Trait ParseTraceId
 * @package App\Traits
 */
trait ParseTraceId
{
    /**
     * Parse Trace Id from traceparent header
     *
     * @param string|null $value
     *
     * @return string|null
     */
    public static function parseTraceId(?string $value): ?string
    {
        $parts = explode('-', $value, 3);

        return $parts[1] ?? null;
    }
}
