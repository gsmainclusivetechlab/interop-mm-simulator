<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static RECEIVED()
 * @method static static PENDING()
 * @method static static ACCEPTED()
 * @method static static REJECTED()
 */
final class TransactionRequestStateEnum extends Enum
{
    const RECEIVED = 'RECEIVED';
    const PENDING = 'PENDING';
    const ACCEPTED = 'ACCEPTED';
    const REJECTED = 'REJECTED';
}
