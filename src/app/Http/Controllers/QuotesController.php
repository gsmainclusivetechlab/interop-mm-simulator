<?php


namespace App\Http\Controllers;

use App\Events\TransactionFailed;
use App\Concerns\InteractsWithHeaders;
use App\Http\Requests\QuotationsCreate;
use App\Http\Requests\QuoteCreate;
use App\Http\Requests\QuoteError;
use App\Http\Requests\QuoteUpdate;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class QuotesController extends Controller
{
    use InteractsWithHeaders;

    /**
     * @param QuotationsCreate $request
     *
     * @return array
     */
    public function storeQuotations(QuotationsCreate $request)
    {
        app()->terminating(function() use ($request) {
            $client = new Client();
            $client->request(
                'POST',
                Env::get('HOST_QUOTING_SERVICE') . '/quotes',
                [
                    'headers' => [
                        'traceparent'        => $request->header('traceparent'),
                        'Accept'             => 'application/vnd.interoperability.quotes+json',
                        'Content-Type'       => 'application/vnd.interoperability.quotes+json;version=1.0',
                        'Date'               => (new Carbon())->toRfc7231String(),
                        'FSPIOP-Source'      => 'payerfsp',
                        'FSPIOP-Destination' => 'payeefsp',
                        'authorization'      => 'Bearer {{TESTFSP1_BEARER_TOKEN}}',
                        'FSPIOP-Signature'   => '{"signature":"iU4GBXSfY8twZMj1zXX1CTe3LDO8Zvgui53icrriBxCUF_wltQmnjgWLWI4ZUEueVeOeTbDPBZazpBWYvBYpl5WJSUoXi14nVlangcsmu2vYkQUPmHtjOW-yb2ng6_aPfwd7oHLWrWzcsjTF-S4dW7GZRPHEbY_qCOhEwmmMOnE1FWF1OLvP0dM0r4y7FlnrZNhmuVIFhk_pMbEC44rtQmMFv4pm4EVGqmIm3eyXz0GkX8q_O1kGBoyIeV_P6RRcZ0nL6YUVMhPFSLJo6CIhL2zPm54Qdl2nVzDFWn_shVyV0Cl5vpcMJxJ--O_Zcbmpv6lxqDdygTC782Ob3CNMvg\\",\\"protectedHeader\\":\\"eyJhbGciOiJSUzI1NiIsIkZTUElPUC1VUkkiOiIvdHJhbnNmZXJzIiwiRlNQSU9QLUhUVFAtTWV0aG9kIjoiUE9TVCIsIkZTUElPUC1Tb3VyY2UiOiJPTUwiLCJGU1BJT1AtRGVzdGluYXRpb24iOiJNVE5Nb2JpbGVNb25leSIsIkRhdGUiOiIifQ"}',
                    ],
                    'json' => $request->mapInTo(),
                ]
            );
        });

        return $request->all();
    }

    /**
     * POST /quotes from mojaloop
     *
     * @param QuoteCreate $request
     *
     * @return string
     * @throws \Exception
     */
    public function store(QuoteCreate $request)
    {
        app()->terminating(function() use ($request) {
            if ($request->amount['amount'] === '51.03') {
                $response = (new \App\Http\OutgoingRequests\QuoteError([
                    'errorInformation' => [
                        'errorCode' => '5103',
                        'errorDescription' => ''
                    ]
                ], [
                    'traceparent'        => $request->header('traceparent'),
                    'FSPIOP-Source'      => $request->header('FSPIOP-Destination'),
                    'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
                ], $request->quoteId))->send();

                if ($response->getStatusCode() === 200) {
                    event(new TransactionFailed());
                }

                return;
            }

            (new \App\Http\OutgoingRequests\QuoteUpdate($request->mapInTo(), [
                'traceparent'        => $request->header('traceparent'),
                'FSPIOP-Source'      => $request->header('FSPIOP-Destination'),
                'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
            ], $request->quoteId))->send();
        });

        return new Response(
        	202,
            [
            	'Content-Type' => 'application/json',
            	'X-Date' => $this->headerXDate()
			]
		);;
    }

    /**
     * @param QuoteUpdate $request
     * @param $id
     */
    public function update(QuoteUpdate $request, $id)
    {
    }

    /**
     * @param QuoteError $request
     * @param $id
     *
     * @throws \Exception
     */
    public function error(QuoteError $request, $id)
    {
    	event(new TransactionFailed());

        return new Response(
        	200,
            [
            	'Content-Type' => 'application/json',
            	'X-Date' => $this->headerXDate()
			]
		);
    }
}
