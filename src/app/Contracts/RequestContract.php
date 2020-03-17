<?php

namespace App\Contracts;

use Psr\Http\Message\ResponseInterface;

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
    public function send(): ResponseInterface;
}
