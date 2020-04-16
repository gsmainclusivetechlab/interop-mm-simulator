<?php

namespace App\Concerns;

use App\Enums\ApiTypeEnum;
use App\Facades\OpenApiValidator;

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
        return OpenApiValidator::make($this->all(), [])->init(new ApiTypeEnum(ApiTypeEnum::Mojaloop), $this);
    }
}
