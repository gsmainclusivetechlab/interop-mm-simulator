<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * The harmonised Transaction Type
 *
 * @method static static billpay()
 * @method static static deposit()
 * @method static static disbursement()
 * @method static static transfer()
 * @method static static merchantpay()
 * @method static static inttransfer()
 * @method static static adjustment()
 * @method static static reversal()
 * @method static static withdrawal()
 */
final class TypeEnum extends Enum
{
    const billpay = 'billpay';
    const deposit = 'deposit';
    const disbursement = 'disbursement';
    const transfer = 'transfer';
    const merchantpay = 'merchantpay';
    const inttransfer = 'inttransfer';
    const adjustment = 'adjustment';
    const reversal = 'reversal';
    const withdrawal = 'withdrawal';
}
