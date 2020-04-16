<?php

namespace App\Concerns;

use Carbon\Carbon;

/**
 * Create specific http headers for request
 *
 * @package App\Http
 */
trait InteractsWithHeaders
{
    /**
     * @return string
     * @throws \Exception
     */
    public function headerXDate()
	{
		return Carbon::now()->toRfc3339String();
	}
}
