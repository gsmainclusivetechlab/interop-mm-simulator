<?php

namespace App\Concerns;

use App\Enums\ApiTypeEnum;
use App\Facades\OpenApiValidator;

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
        return OpenApiValidator::make($this->all(), [])->init(new ApiTypeEnum(ApiTypeEnum::MM), $this);
    }
}
