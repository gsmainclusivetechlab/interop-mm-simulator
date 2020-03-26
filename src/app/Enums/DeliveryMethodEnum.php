<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class DeliveryMethodEnum extends Enum
{
	const directtoaccount = 'directtoaccount';
	const agent = 'agent';
	const personaldelivery = 'personaldelivery';
}
