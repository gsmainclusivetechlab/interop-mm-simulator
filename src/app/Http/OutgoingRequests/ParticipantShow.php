<?php

namespace App\Requests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Env;

/**
 * Class ParticipantShow
 *
 * @package App\Requests
 */
class ParticipantShow extends BaseRequest
{
    /**
     * ParticipantShow constructor.
     *
     * @param array $data
     * @param array $headers
     * @param $type
     * @param $id
     * @throws \Exception
     */
    public function __construct(array $data, array $headers, $type, $id)
    {
        parent::__construct(
            $data,
            $headers,
            Env::get('HOST_ACCOUNT_LOOKUP_SERVICE') . "participants/{$type}/{$id}"
        );

        $this->method = 'GET';

        $this->headers['Accept'] = 'application/vnd.interoperability.participants+json';
        $this->headers['Content-Type'] = 'application/vnd.interoperability.participants+json;version=1.0';
        $this->headers['Date'] = (new Carbon())->toRfc7231String();
        $this->headers['FSPIOP-Source'] = Env::get('FSPIOP_SOURCE');
    }
}
