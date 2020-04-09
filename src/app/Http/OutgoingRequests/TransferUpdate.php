<?php

namespace App\Requests;

use Carbon\Carbon;
use Illuminate\Support\Env;

class TransferUpdate extends BaseRequest
{
    public function __construct(array $data, array $headers, string $url)
    {
        parent::__construct($data, $headers, Env::get('HOST_ML_API_ADAPTER') . 'transfers/' . $url);

        $this->method = 'PUT';

        $this->headers['Content-Type'] = 'application/vnd.interoperability.transfers+json;version=1.0';
        $this->headers['Date'] = (new Carbon())->toRfc7231String();
    }
}
