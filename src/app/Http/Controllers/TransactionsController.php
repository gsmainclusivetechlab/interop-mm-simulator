<?php


namespace App\Http\Controllers;

use App\Http\Headers;
use App\Http\Requests\TransactionCreate;
use App\Http\TriggerRulesSets;
use App\Models\Transaction;
use App\Requests\ParticipantShow;
use App\Requests\TransactionRequest;
use GuzzleHttp\Psr7\Response;
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

            if (TriggerRulesSets::participantMerchant($data['payer']['partyIdentifier'])) {
                (new ParticipantShow(
                    $data,
                    [
                        'traceparent' => $request->header('traceparent'),
                    ],
                    strtoupper($data['payer']['partyIdType']),
                    $data['payer']['partyIdentifier']
                ))->send();
            } else {
                (new TransactionRequest($data, [
                    'traceparent' => $request->header('traceparent'),
                    'FSPIOP-Destination' => Env::get('FSPIOP_DESTINATION'),
                ]))->send();
            }
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
