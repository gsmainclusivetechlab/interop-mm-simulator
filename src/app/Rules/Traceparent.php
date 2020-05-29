<?php

namespace App\Rules;

use App\Traits\ParseTraceId;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class Traceparent
 * @package App\Rules
 */
class Traceparent implements Rule
{
    use ParseTraceId;

    /**
     * @inheritDoc
     */
    public function passes($attribute, $value): bool
    {
        if (!$value) {
            return false;
        }

        $traceId = self::parseTraceId($value);

        return strlen($traceId) === 32 &&
            ctype_xdigit($traceId) &&
            hexdec($traceId);
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return __('Header traceparent has wrong format!');
    }
}
