<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PAYER()
 * @method static static PAYEE()
 */
final class TransactionInitiatorEnum extends Enum
{
    const PAYER = 'PAYER';
    const PAYEE = 'PAYEE';
}
