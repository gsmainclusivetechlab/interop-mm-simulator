<?php

namespace App\Concerns;

use App\Enums\ApiTypeEnum;
use App\Http\OpenApiValidator;

/**
 * Trait InteractsWithMMValidator
 * @package App\Concerns
 */
trait InteractsWithMMValidator
{
    /**
     * @return OpenApiValidator
     */
    public function validator()
    {
        return new OpenApiValidator(new ApiTypeEnum(ApiTypeEnum::MM), $this);
    }
}
