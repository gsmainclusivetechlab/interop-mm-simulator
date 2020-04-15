<?php

namespace App\Concerns;

use App\Enums\ApiTypeEnum;
use App\Http\OpenApiValidator;

/**
 * Trait InteractsWithMojaloopValidator
 * @package App\Concerns
 */
trait InteractsWithMojaloopValidator
{
    /**
     * @return OpenApiValidator
     */
    public function validator()
    {
        return new OpenApiValidator(new ApiTypeEnum(ApiTypeEnum::Mojaloop), $this);
    }
}
