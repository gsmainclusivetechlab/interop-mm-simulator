<?php

namespace App\Http;

use Carbon\Carbon;

class Headers
{
	public static function getXDate()
	{
		return (new Carbon())->toRfc3339String();
	}
}