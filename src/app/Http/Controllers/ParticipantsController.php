<?php

namespace App\Http\Controllers;

use App\Http\Headers;
use App\Http\Requests\TransactionCreate;
use App\Http\TriggerRulesSets;
use App\Models\Transaction;
use App\Requests\QuoteStore;
use App\Requests\TransactionRequest;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Env;

/**
 * Class ParticipantsController
 * @package App\Http\Controllers
 */
class ParticipantsController extends Controller
{
    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return Response
     */
    public function update(Request $request, $type, $id)
    {
        app()->terminating(function() use ($request, $id) {
            if (TriggerRulesSets::participantMerchant($id)) {
                $transaction = Transaction::getCurrent();
                $transactionRequest = (new TransactionCreate())->merge($transaction->attributesToArray());

                (new TransactionRequest($transactionRequest->mapInTo(), [
                    'traceparent' => $request->header('traceparent'),
                    'FSPIOP-Destination' => $request->fspId,
                ]))->send();
            } elseif (TriggerRulesSets::participantP2p($id)) {
                (new QuoteStore(QuoteStore::mapInTo($id), [
                    'traceparent' => $request->header('traceparent'),
                    'FSPIOP-Destination' => $request->fspId,
                ]))->send();
            }
        });

        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'X-Date' => Headers::getXDate()
            ]
        );
    }

    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return Response
     */
    public function error(Request $request, $type, $id)
    {
        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'X-Date' => Headers::getXDate()
            ]
        );
    }
}
