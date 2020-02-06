<?php


namespace App\Http\Controllers;

use App\Http\Requests\QuotationsCreate;
use App\Http\Requests\QuoteCreate;
use App\Http\Requests\QuoteError;
use App\Http\Requests\QuoteUpdate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class QuotesController extends Controller
{
    /**
     * @param QuotationsCreate $request
     * @return array
     */
    public function storeQuotations(QuotationsCreate $request)
    {
        $headers = [
            'Accept'             => 'application/vnd.interoperability.quotes+json',
            'Content-Type'       => 'application/vnd.interoperability.quotes+json;version=1.0',
            'Date'               => date('D, d M Y H:i:s') . ' GMT',
            'FSPIOP-Source'      => 'payerfsp',
            'FSPIOP-Destination' => 'payeefsp',
            'authorization'      => 'Bearer {{TESTFSP1_BEARER_TOKEN}}',
            'FSPIOP-Signature'   => '{"signature":"iU4GBXSfY8twZMj1zXX1CTe3LDO8Zvgui53icrriBxCUF_wltQmnjgWLWI4ZUEueVeOeTbDPBZazpBWYvBYpl5WJSUoXi14nVlangcsmu2vYkQUPmHtjOW-yb2ng6_aPfwd7oHLWrWzcsjTF-S4dW7GZRPHEbY_qCOhEwmmMOnE1FWF1OLvP0dM0r4y7FlnrZNhmuVIFhk_pMbEC44rtQmMFv4pm4EVGqmIm3eyXz0GkX8q_O1kGBoyIeV_P6RRcZ0nL6YUVMhPFSLJo6CIhL2zPm54Qdl2nVzDFWn_shVyV0Cl5vpcMJxJ--O_Zcbmpv6lxqDdygTC782Ob3CNMvg\\",\\"protectedHeader\\":\\"eyJhbGciOiJSUzI1NiIsIkZTUElPUC1VUkkiOiIvdHJhbnNmZXJzIiwiRlNQSU9QLUhUVFAtTWV0aG9kIjoiUE9TVCIsIkZTUElPUC1Tb3VyY2UiOiJPTUwiLCJGU1BJT1AtRGVzdGluYXRpb24iOiJNVE5Nb2JpbGVNb25leSIsIkRhdGUiOiIifQ"}',
        ];
        $client = new Client();

        try {
            $client->request(
                'POST',
                Env::get('HOST_QUOTING_SERVICE') . '/quotes',
                [
                    'headers' => $headers,
                    'json' => $request->mapInTo(),
                    'debug' => true,
                ]
            );

            return $request->all();
        } catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
    }

    /**
     * TODO initiate PUT quotes to mojaloop
     * @param QuoteCreate $request
     * @return string
     */
    public function store(QuoteCreate $request)
    {
        Log::info(
            'PUT /quotes' . PHP_EOL
            . 'h: ' . $request->headers . PHP_EOL
            . 'b: ' . $request->getContent() . PHP_EOL
        );
    }


    /**
     * @param QuoteUpdate $request
     * @param $id
     */
    public function update(QuoteUpdate $request, $id)
    {
        Log::info(
            'PUT /quotes' . PHP_EOL
            . 'h: ' . $request->headers . PHP_EOL
            . 'b: ' . $request->getContent() . PHP_EOL
            . 'id = ' . $id . PHP_EOL
        );
    }


    /**
     * @param QuoteError $request
     * @param $id
     */
    public function error(QuoteError $request, $id)
    {
        Log::info(
            'PUT /quotes/{id}/error' . PHP_EOL
            . 'body: ' . $request->getContent() . PHP_EOL
            . 'id = ' . $id . PHP_EOL
        );
    }
}
