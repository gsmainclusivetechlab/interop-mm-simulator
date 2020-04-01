<?php

namespace App\Http\Controllers;

use App\Events\TransactionFailed;
use App\Events\TransactionSuccess;
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
            event(new TransactionSuccess());

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
     * @param TransferError $request
     * @param $id
     */
    public function error(TransferError $request, $id)
    {
    	event(new TransactionFailed());
    }
}
