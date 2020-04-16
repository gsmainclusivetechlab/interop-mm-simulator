<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class OpenApiValidator
 * @package App\Facades
 */
class OpenApiValidator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'OpenApiValidator';
    }
}
