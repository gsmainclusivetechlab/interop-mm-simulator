<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static MM()
 * @method static static Mojaloop()
 */
final class ApiTypeEnum extends Enum
{
    const MM = 'mm';
    const Mojaloop = 'mojaloop';
}
