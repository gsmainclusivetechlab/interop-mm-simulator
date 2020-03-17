<?php


namespace App\Http\Controllers;

use App\Http\Requests\TransactionCreate;
use App\Models\Transaction;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Str;

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
     *
     * @return Response
     */
    public function store(TransactionCreate $request): Response
    {
        if ($request->traceId) {
            $data = $request->all();

            $data['trace_id'] = $request->traceId;
            $data['callback_url'] = $request->header('x-callback-url');

            if (!$request->transactionStatus) {
                $data['transactionStatus'] = 'pending';
            }

            Transaction::create($data);
        }

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
                        'FSPIOP-Source'      => Env::get('FSPIOP_SOURCE'),
                        'FSPIOP-Destination' => Env::get('FSPIOP_DESTINATION'),
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

        $response = [
            'status' => 'pending',
            'notificationMethod' => "callback",
            'serverCorrelationId' => $request->header('X-CorrelationID') ?? Str::uuid()
        ];

        return new Response(
            202,
            ['X-Date' => (new Carbon())->toRfc7231String()],
            \GuzzleHttp\json_encode($response)
        );
    }
}
