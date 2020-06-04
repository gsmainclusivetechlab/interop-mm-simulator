<?php

namespace App\Http\Controllers;

use app\Events\TransactionFailed;
use app\Http\Headers;
use app\Http\Requests\QuoteCreate;
use app\Http\Requests\QuoteUpdate;
use app\Http\TriggerRulesSets;
use app\Models\Transaction;
use app\Requests\QuoteError;
use App\Requests\TransferStore;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class QuotesController extends Controller
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Create a new controller instance.
     *
     * @param Client $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * POST /quotes from mojaloop
     *
     * @param QuoteCreate $request
     * @return string
     */
    public function store(QuoteCreate $request)
    {
        app()->terminating(function () use ($request) {
            if (TriggerRulesSets::amountQuote($request->amount['amount'])) {
                $response = (new QuoteError(
                    [
                        'errorInformation' => [
                            'errorCode' => '5103',
                            'errorDescription' =>
                                'Payee FSP does not want to proceed with the financial transaction after receiving a quote.',
                        ],
                    ],
                    [
                        'traceparent' => $request->header('traceparent'),
                        'FSPIOP-Source' => $request->header(
                            'FSPIOP-Destination'
                        ),
                        'FSPIOP-Destination' => $request->header(
                            'FSPIOP-Source'
                        ),
                    ],
                    $request->quoteId
                ))->send();

                if ($response->getStatusCode() === 200) {
                    event(new TransactionFailed());
                }

                return;
            }

            $transaction = Transaction::getCurrent();
            if (
                empty($transaction->transactionId) &&
                !empty($request->transactionRequestId)
            ) {
                $transaction->update([
                    'transactionId' => $request->transactionRequestId,
                ]);
            }

            (new \App\Requests\QuoteUpdate(
                $request->mapInTo(),
                [
                    'traceparent' => $request->header('traceparent'),
                    'FSPIOP-Source' => $request->header('FSPIOP-Destination'),
                    'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
                ],
                $request->quoteId
            ))->send();
        });

        return new Response(202, [
            'Content-Type' => 'application/json',
            'X-Date' => Headers::getXDate(),
        ]);
    }

    /**
     * @param QuoteUpdate $request
     * @return Response
     */
    public function update(QuoteUpdate $request)
    {
        app()->terminating(function () use ($request) {
            $req = new TransferStore(
                $request->mapInTo(),
                [
                    'traceparent' => $request->header('traceparent'),
                    'FSPIOP-Source' => $request->header('FSPIOP-Destination'),
                    'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
                ],
                $this->client);
            $req->send();
        });

        return new Response(200, [
            'Content-Type' => 'application/json',
            'X-Date' => Headers::getXDate(),
        ]);
    }

    /**
     * @return Response
     */
    public function error()
    {
        return new Response(200, [
            'Content-Type' => 'application/json',
            'X-Date' => Headers::getXDate(),
        ]);
    }
}
