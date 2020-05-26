<?php

namespace App\Http\Controllers;

use App\Events\TransactionFailed;
use App\Events\TransactionSuccess;
use App\Http\Headers;
use App\Http\Requests\TransferCreate;
use App\Http\Requests\TransferError;
use App\Http\Requests\TransferUpdate;
use App\Http\TriggerRulesSets;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;

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
        $completedTimestamp = (new Carbon())->toIso8601ZuluString('millisecond');
        app()->terminating(function() use ($request, $completedTimestamp) {
            if (TriggerRulesSets::amountTransfer($request->amount['amount'])) {
                $response = (new \App\Requests\TransferError([
                    'errorInformation' => [
                        'errorCode' => '5001',
                        'errorDescription' => 'Payee FSP has insufficient liquidity to perform the transfer.'
                    ]
                ], [
                    'traceparent'        => $request->header('traceparent'),
                    'FSPIOP-Source'      => $request->header('FSPIOP-Destination'),
                    'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
                ], $request->transferId))->send();

                if ($response->getStatusCode() === 200) {
                    event(new TransactionFailed());
                }

                return;
            }

            event(new TransactionSuccess());

            $data = $request->mapInTo($completedTimestamp);

            (new \App\Requests\TransferUpdate($data, [
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
     * @param TransferUpdate $request
     * @param $id
     * @return Response
     */
    public function update(TransferUpdate $request, $id)
    {
        return new Response(
            200,
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
