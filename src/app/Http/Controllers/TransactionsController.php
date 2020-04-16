<?php

namespace App\Http\Controllers;

use App\Concerns\InteractsWithHeaders;
use App\Http\Requests\TransactionCreate;
use App\Models\Transaction;
use App\Http\OutgoingRequests\TransactionRequest;
use App\Traits\ParseTraceId;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;

/**
 * Class TransactionsController
 *
 * @package App\Http\Controllers
 */
class TransactionsController extends Controller
{
    use ParseTraceId,
        InteractsWithHeaders;

    /**
     * Request from service provider
     * Create A Transaction
     *
     * @param TransactionCreate $request
     *
     * @return Response
     * @throws \Exception
     */
    public function store(TransactionCreate $request): Response
    {
        $traceId = self::parseTraceId($request->header('traceparent'));

        if ($traceId) {
            $data = $request->all();

            $data['trace_id'] = $traceId;
            $data['callback_url'] = $request->header('x-callback-url');

            if (!$request->transactionStatus) {
                $data['transactionStatus'] = 'pending';
            }

            Transaction::create($data);
        }

        app()->terminating(function() use ($request) {
            $data = $request->mapInTo();

            $response = (new TransactionRequest($data, [
				'traceparent'        => $request->header('traceparent'),
			]))->send();

            \Illuminate\Support\Facades\Log::info(
                'POST /transactionRequests ' . $response->getStatusCode() . PHP_EOL
                . json_encode($data) . PHP_EOL
            );
        });

        $response = [
            'status' => 'pending',
            'notificationMethod' => "callback",
            'serverCorrelationId' => $request->header('X-CorrelationID') ?: Str::uuid()
        ];

        return new Response(
            202,
            [
            	'Content-Type' => 'application/json',
            	'X-Date' => $this->headerXDate()
			],
            json_encode($response)
        );
    }
}
