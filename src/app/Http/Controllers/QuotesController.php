<?php

namespace App\Http\Controllers;

use App\Events\TransactionFailed;
use App\Http\Headers;
use App\Http\Requests\QuoteCreate;
use App\Http\Requests\QuoteError;
use App\Http\Requests\QuoteUpdate;
use App\Http\TriggerRulesSets;
use App\Models\Transaction;
use GuzzleHttp\Psr7\Response;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class QuotesController extends Controller
{
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
                $response = (new \App\Requests\QuoteError(
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
     * @param $id
     * @return Response
     */
    public function update(QuoteUpdate $request, $id)
    {
        app()->terminating(function () use ($request) {
            (new \App\Requests\TransferStore($request->mapInTo(), [
                'traceparent' => $request->header('traceparent'),
                'FSPIOP-Source' => $request->header('FSPIOP-Destination'),
                'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
            ]))->send();
        });

        return new Response(200, [
            'Content-Type' => 'application/json',
            'X-Date' => Headers::getXDate(),
        ]);
    }

    /**
     * @param QuoteError $request
     * @param $id
     */
    public function error(QuoteError $request, $id)
    {
        return new Response(200, [
            'Content-Type' => 'application/json',
            'X-Date' => Headers::getXDate(),
        ]);
    }
}
