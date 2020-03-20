<?php

namespace App\Requests;

use Illuminate\Support\Env;

/**
 * Class Callback
 *
 * @package App\Requests
 */
class Callback extends BaseRequest
{
    public function __construct(array $data, array $headers, string $url)
    {
        parent::__construct($data, $headers, Env::get('HOST_SERVICE_PROVIDER') . $url);

        $this->method = 'PUT';
    }
}
