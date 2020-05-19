<?php


namespace App\Http\Controllers;

use App\Http\Headers;
use App\Http\Requests\TransactionCreate;
use App\Models\Transaction;
use App\Requests\TransactionRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
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

//            $client = new Client();
//            $idType = $data['payer']['partyIdType'];
//            $identifier = $data['payer']['partyIdentifier'];
//            $client->request(
//                'GET',
//                Env::get('HOST_ACCOUNT_LOOKUP_SERVICE') . "participants/{$idType}/{$identifier}",
//            [
//                'headers' => [
//                    'traceparent'        => $request->header('traceparent'),
//                    'Accept'             => 'application/vnd.interoperability.participants+json',
//                    'Content-Type'       => 'application/vnd.interoperability.participants+json;version=1.0',
//                    'Date'               => (new Carbon())->toRfc7231String(),
//                    'FSPIOP-Source'      => Env::get('FSPIOP_SOURCE'),
//                ]
//            ]
//            );

            /**
             * TODO make request after get response PUT /participants
             */
            $response = (new TransactionRequest($data, [
				'traceparent'        => $request->header('traceparent'),
			]))->send();
        });

        $response = [
            'status' => 'pending',
            'notificationMethod' => "callback",
            'serverCorrelationId' => $request->header('X-CorrelationID') ?? Str::uuid()
        ];

        return new Response(
            202,
            [
            	'Content-Type' => 'application/json',
            	'X-Date' => Headers::getXDate()
			],
            \GuzzleHttp\json_encode($response)
        );
    }
}
