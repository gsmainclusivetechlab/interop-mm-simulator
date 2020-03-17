<?php

namespace App\Http\Controllers;

use App\Events\TerminateTransaction;
use App\Http\Requests\TransferCreate;
use App\Http\Requests\TransferError;
use App\Http\Requests\TransferUpdate;
use App\Models\Transaction;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class TransfersController extends Controller
{
    /**
     * Handle transfer request
     *
     * @param TransferCreate $request
     *
     * @return Response
     */
    public function store(TransferCreate $request): Response
    {
        app()->terminating(function() use ($request) {
            $transaction = Transaction::getCurrent();

            $transaction->update(['transactionStatus' => $this->request->transferState ?? 'COMPLETED']);

            event(new TerminateTransaction($transaction));

            $data = $request->mapInTo();

            (new \App\Requests\TransferUpdate($data, [
                'traceparent'        => $request->header('traceparent'),
                'FSPIOP-Source'      => $request->header('FSPIOP-Destination'),
                'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
            ], $request->transferId))->send();
        });

        return new Response(202);
    }

    /**
     * @param TransferUpdate $request
     * @param $id
     */
    public function update(TransferUpdate $request, $id)
    {
    }

    /**
     * @param TransferError $request
     * @param $id
     */
    public function error(TransferError $request, $id)
    {
    }
}
