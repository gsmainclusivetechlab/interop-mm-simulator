<?php

namespace App\Http\Controllers;

use App\Events\TransactionFailed;
use App\Events\TransactionSuccess;
use App\Http\OutgoingRequests\Headers;
use App\Http\OutgoingRequests\TransferUpdate;
use App\Http\Requests\TransferCreate;
use App\Http\Requests\TransferError;
use GuzzleHttp\Psr7\Response;

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

            (new TransferUpdate($data, [
                'traceparent'        => $request->header('traceparent'),
                'FSPIOP-Source'      => $request->header('FSPIOP-Destination'),
                'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
            ], $request->transferId))->send();
        });

        return new Response(
        	202,
            [
            	'Content-Type' => 'application/json',
            	'X-Date' => Headers::getXDate()
			]
		);
    }

    /**
     * @param TransferError $request
     * @param $id
     */
    public function error(TransferError $request, $id)
    {
    	event(new TransactionFailed());

        return new Response(
        	200,
            [
            	'Content-Type' => 'application/json',
            	'X-Date' => Headers::getXDate()
			]
		);
    }
}
