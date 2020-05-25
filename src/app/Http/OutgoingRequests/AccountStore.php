<?php

namespace App\Requests;

use App\Http\Headers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;

/**
 * Class AccountStore
 *
 * @package App\Requests
 */
class AccountStore extends BaseRequest
{
    /**
     * AccountStore constructor.
     *
     * @param array $data
     * @param array $headers
     * @param $identifierType
     * @param $identifier
     */
    public function __construct(array $data, array $headers, $identifierType, $identifier)
    {
        parent::__construct(
            $data,
            $headers,
            Env::get('HOST_SERVICE_PROVIDER') . "accounts/{$identifierType}/{$identifier}/authorisationcodes"
        );

        $this->method = 'POST';

        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Date'] = Headers::getXDate();
    }
}
