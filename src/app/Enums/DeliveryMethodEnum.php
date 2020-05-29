<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static directtoaccount()
 * @method static static agent()
 * @method static static personaldelivery()
 */
final class DeliveryMethodEnum extends Enum
{
    const directtoaccount = 'directtoaccount';
    const agent = 'agent';
    const personaldelivery = 'personaldelivery';
}
