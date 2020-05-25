<?php

namespace App\Http;

/**
 * Trigger rules sets
 *
 * @package App\Http
 */
class TriggerRulesSets
{

    /**
     * Amount triggers
     *
     * @param $amount
     * @return bool
     */
	public static function amount($amount): bool
	{
		return boolval(preg_match('/^(3|5)[0-9][.]\d{2}/', $amount));
	}

    /**
     * Participant merchant triggers
     *
     * @param $id
     * @return bool
     */
    public static function participantMerchant($id): bool
    {
        return boolval(preg_match('/^[+]?\d{6}9{4}/', $id));
    }

    /**
     * Participant P2P triggers
     *
     * @param $id
     * @return bool
     */
    public static function participantP2p($id): bool
    {
        return boolval(preg_match('/^[+]?\d{6}8{4}/', $id));
    }
}
