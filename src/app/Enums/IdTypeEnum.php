<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class IdTypeEnum extends Enum
{
	const passport = 'passport';
	const nationalregistration = 'nationalregistration';
	const otherId = 'otherId';
	const drivinglicence = 'drivinglicence';
	const socialsecurity = 'socialsecurity';
	const alienregistration = 'alienregistration';
	const nationalidcard = 'nationalidcard';
	const employer = 'employer';
	const taxid = 'taxid';
	const seniorcitizenscard = 'seniorcitizenscard';
	const marriagecertificate = 'marriagecertificate';
	const birthcertificate = 'birthcertificate';
	const healthcard = 'healthcard';
	const votersid = 'votersid';
	const villageelderLetter = 'villageelderLetter';
	const pancard = 'pancard';
	const officialletter = 'officialletter';
}
