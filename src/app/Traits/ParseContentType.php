<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use function GuzzleHttp\Psr7\parse_header;

/**
 * Trait ParseContentType
 * @package App\Traits
 */
trait ParseContentType
{
    /**
     * @return string|null
     */
    public function parseContentType(): ?string
    {
        $headerParts = parse_header($this->request->header('content-type'));

        return Arr::get(Arr::get($headerParts, 0), 0);
    }
}
