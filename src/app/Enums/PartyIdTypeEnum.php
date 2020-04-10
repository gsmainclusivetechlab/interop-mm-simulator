<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static MSISDN()
 * @method static static EMAIL()
 * @method static static PERSONAL_ID()
 * @method static static BUSINESS()
 * @method static static DEVICE()
 * @method static static ACCOUNT_ID()
 * @method static static IBAN()
 * @method static static ALIAS()
 */
final class PartyIdTypeEnum extends Enum
{
	const MSISDN = 'MSISDN';
	const EMAIL = 'EMAIL';
	const PERSONAL_ID = 'PERSONAL_ID';
	const BUSINESS = 'BUSINESS';
	const DEVICE = 'DEVICE';
	const ACCOUNT_ID = 'ACCOUNT_ID';
	const IBAN = 'IBAN';
	const ALIAS = 'ALIAS';
}
