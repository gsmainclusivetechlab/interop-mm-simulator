<?php

namespace App\Requests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Env;

/**
 * Class QuoteUpdate
 *
 * @package App\Requests
 */
class QuoteUpdate extends BaseRequest
{
    /**
     * QuoteUpdate constructor.
     *
     * @param array $data
     * @param array $headers
     * @param string|null $url
     */
    public function __construct(array $data, array $headers, ?string $url)
    {
        parent::__construct(
            $data,
            $headers,
            Env::get('HOST_QUOTING_SERVICE') . 'quotes/' . $url
        );

        $this->method = 'PUT';

        $this->headers['Content-Type'] =
            'application/vnd.interoperability.quotes+json;version=1.0';
        $this->headers['Date'] = (new Carbon())->toRfc7231String();
        $this->headers['FSPIOP-Signature'] =
            '{"signature":"iU4GBXSfY8twZMj1zXX1CTe3LDO8Zvgui53icrriBxCUF_wltQmnjgWLWI4ZUEueVeOeTbDPBZazpBWYvBYpl5WJSUoXi14nVlangcsmu2vYkQUPmHtjOW-yb2ng6_aPfwd7oHLWrWzcsjTF-S4dW7GZRPHEbY_qCOhEwmmMOnE1FWF1OLvP0dM0r4y7FlnrZNhmuVIFhk_pMbEC44rtQmMFv4pm4EVGqmIm3eyXz0GkX8q_O1kGBoyIeV_P6RRcZ0nL6YUVMhPFSLJo6CIhL2zPm54Qdl2nVzDFWn_shVyV0Cl5vpcMJxJ--O_Zcbmpv6lxqDdygTC782Ob3CNMvg\\",\\"protectedHeader\\":\\"eyJhbGciOiJSUzI1NiIsIkZTUElPUC1VUkkiOiIvdHJhbnNmZXJzIiwiRlNQSU9QLUhUVFAtTWV0aG9kIjoiUE9TVCIsIkZTUElPUC1Tb3VyY2UiOiJPTUwiLCJGU1BJT1AtRGVzdGluYXRpb24iOiJNVE5Nb2JpbGVNb25leSIsIkRhdGUiOiIifQ"}';
    }
}
