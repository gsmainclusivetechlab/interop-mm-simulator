<?php


namespace App\Http\Controllers;

use App\Http\Requests\TransactionCreate;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;

/**
 * Class TransactionsController
 *
 * @package App\Http\Controllers
 */
class TransactionsController extends Controller
{
    /**
     * Request from service provider
     * Create A Transaction
     *
     * @param TransactionCreate $request
     * @return mixed
     */
    public function store(TransactionCreate $request)
    {
        app()->terminating(function() use ($request) {
            $data = $request->mapInTo();

            $client = new Client();
            $response = $client->request(
                'POST',
                Env::get('HOST_TRANSACTION_REQUESTS_SERVICE') . '/transactionRequests',
                [
                    'headers' => [
                        'traceparent'        => $request->header('traceparent'),
                        'Accept'             => 'application/vnd.interoperability.transactionRequests+json;version=1',
                        'Content-Type'       => 'application/vnd.interoperability.transactionRequests+json;version=1.0',
                        'Date'               => (new Carbon())->toRfc7231String(),
                        'FSPIOP-Source'      => 'payeefsp',
                        'FSPIOP-Destination' => 'testfsp1',
                        'FSPIOP-Signature'   => '{"signature":"iU4GBXSfY8twZMj1zXX1CTe3LDO8Zvgui53icrriBxCUF_wltQmnjgWLWI4ZUEueVeOeTbDPBZazpBWYvBYpl5WJSUoXi14nVlangcsmu2vYkQUPmHtjOW-yb2ng6_aPfwd7oHLWrWzcsjTF-S4dW7GZRPHEbY_qCOhEwmmMOnE1FWF1OLvP0dM0r4y7FlnrZNhmuVIFhk_pMbEC44rtQmMFv4pm4EVGqmIm3eyXz0GkX8q_O1kGBoyIeV_P6RRcZ0nL6YUVMhPFSLJo6CIhL2zPm54Qdl2nVzDFWn_shVyV0Cl5vpcMJxJ--O_Zcbmpv6lxqDdygTC782Ob3CNMvg\\",\\"protectedHeader\\":\\"eyJhbGciOiJSUzI1NiIsIkZTUElPUC1VUkkiOiIvdHJhbnNmZXJzIiwiRlNQSU9QLUhUVFAtTWV0aG9kIjoiUE9TVCIsIkZTUElPUC1Tb3VyY2UiOiJPTUwiLCJGU1BJT1AtRGVzdGluYXRpb24iOiJNVE5Nb2JpbGVNb25leSIsIkRhdGUiOiIifQ"}',
                    ],
                    'json' => $data,
                ]
            );

            \Illuminate\Support\Facades\Log::info(
                'POST /transactionRequests ' . $response->getStatusCode() . PHP_EOL
                . \GuzzleHttp\json_encode($data) . PHP_EOL
            );
        });

        return new Response(
            202,
            ['X-Date' => (new Carbon())->toRfc7231String()],
            \GuzzleHttp\json_encode($request->all())
        );
    }
}
