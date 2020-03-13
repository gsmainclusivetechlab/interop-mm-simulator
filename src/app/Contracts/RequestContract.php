<?php

namespace App\Contracts;

/**
 * Interface RequestContract
 *
 * @package App\Contracts
 */
interface RequestContract
{
    /**
     * Send request
     *
     * @return bool
     */
    public function send(): bool;
}
