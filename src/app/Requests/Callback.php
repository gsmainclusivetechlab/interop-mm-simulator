<?php

namespace App\Requests;

/**
 * Class Callback
 * @package App\Requests
 */
class Callback extends BaseRequest
{
    public function __construct(array $data, array $headers, string $url)
    {
        parent::__construct($data, $headers, $url);

        $this->method = 'PUT';
    }
}
