<?php

namespace App\Http\Controllers;

use App\Enums\TransactionRequestStateEnum;
use App\Events\TransactionFailed;
use App\Http\Headers;
use App\Http\Requests\TransactionRequestError;
use App\Http\Requests\TransactionRequestUpdate;
use App\Models\Transaction;
use GuzzleHttp\Psr7\Response;

/**
 * Mojaloop Request controller
 *
 * Class TransactionRequestsController
 * @package App\Http\Controllers
 */
class TransactionRequestsController extends Controller
{
    /**
     * @param TransactionRequestUpdate $request
     * @param $id
     *
     * @return Response
     */
    public function update(TransactionRequestUpdate $request, $id)
    {
        if (
            $request->transactionRequestState ===
            TransactionRequestStateEnum::REJECTED
        ) {
            event(new TransactionFailed());
        }

        $transaction = Transaction::getCurrent();
        if (empty($transaction->transactionId)) {
            $transaction->update(['transactionId' => $id]);
        }

        return new Response(200, [
            'Content-Type' => 'application/json',
            'X-Date' => Headers::getXDate(),
        ]);
    }

    /**
     * @param TransactionRequestError $request
     * @param $id
     * @return Response
     */
    public function error(TransactionRequestError $request, $id)
    {
        event(new TransactionFailed());

        return new Response(200, [
            'Content-Type' => 'application/json',
            'X-Date' => Headers::getXDate(),
        ]);
    }
}
