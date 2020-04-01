<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SEND()
 * @method static static RECEIVE()
 */
final class AmountTypeEnum extends Enum
{
    const SEND = 'SEND';
    const RECEIVE = 'RECEIVE';
}
