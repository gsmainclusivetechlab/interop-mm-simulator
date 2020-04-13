<?php

namespace App\Http\OutgoingRequests;

use Carbon\Carbon;

/**
 * Create specific http headers for request
 *
 * @package App\Http
 */
class Headers
{
    /**
     * @return string
     * @throws \Exception
     */
    public static function getXDate()
	{
		return Carbon::now()->toRfc3339String();
	}
}
