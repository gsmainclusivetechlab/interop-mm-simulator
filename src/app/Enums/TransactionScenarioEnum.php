<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DEPOSIT()
 * @method static static WITHDRAWAL()
 * @method static static TRANSFER()
 * @method static static PAYMENT()
 * @method static static REFUND()
 */
final class TransactionScenarioEnum extends Enum
{
	const DEPOSIT = 'DEPOSIT';
	const WITHDRAWAL = 'WITHDRAWAL';
	const TRANSFER = 'TRANSFER';
	const PAYMENT = 'PAYMENT';
	const REFUND = 'REFUND';
}
