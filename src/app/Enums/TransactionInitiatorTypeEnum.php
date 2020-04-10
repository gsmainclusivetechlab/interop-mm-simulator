<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static CONSUMER()
 * @method static static AGENT()
 * @method static static BUSINESS()
 * @method static static DEVICE()
 */
final class TransactionInitiatorTypeEnum extends Enum
{
    const CONSUMER = 'CONSUMER';
    const AGENT = 'AGENT';
    const BUSINESS = 'BUSINESS';
    const DEVICE = 'DEVICE';
}
