<?php

namespace App\Http\Controllers;

use App\Events\TransactionFailed;
use App\Events\TransactionSuccess;
use App\Http\Headers;
use App\Http\Requests\TransferCreate;
use App\Http\Requests\TransferError;
use App\Requests\TransferUpdate;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
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
        $completedTimestamp= (new Carbon())->toIso8601ZuluString('millisecond');
        app()->terminating(function() use ($request, $completedTimestamp) {
            event(new TransactionSuccess());

            $data = $request->mapInTo($completedTimestamp);

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

    /**
     * @param \GuzzleHttp\Psr7\Request $request
     * @param $type
     * @param $id
     * @return Response
     */
    public function part(Request $request, $type, $id)
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
