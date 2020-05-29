<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static passport()
 * @method static static nationalregistration()
 * @method static static otherId()
 * @method static static drivinglicence()
 * @method static static socialsecurity()
 * @method static static alienregistration()
 * @method static static nationalidcard()
 * @method static static employer()
 * @method static static taxid()
 * @method static static seniorcitizenscard()
 * @method static static marriagecertificate()
 * @method static static birthcertificate()
 * @method static static healthcard()
 * @method static static votersid()
 * @method static static villageelderLetter()
 * @method static static pancard()
 * @method static static officialletter()
 */
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
