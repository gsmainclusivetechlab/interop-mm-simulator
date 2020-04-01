<?php


namespace App\Http\Controllers;

use App\Events\TransactionFailed;
use App\Events\TransactionSuccess;
use App\Http\Headers;
use App\Models\Transaction;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

/**
 * Mojaloop Request controller
 *
 * Class TransactionRequestsController
 * @package App\Http\Controllers
 */
class TransactionRequestsController extends Controller
{
    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if ($request->transactionRequestState === 'REJECTED') {
            event(new TransactionFailed());
        }

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
     * @return Response
     */
    public function store(Request $request)
    {
        return new Response(200);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function error(Request $request, $id)
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
