<?php


namespace App\Http\Controllers;

use App\Enums\TransactionRequestStateEnum;
use App\Events\TransactionFailed;
use App\Events\TransactionSuccess;
use App\Http\Requests\TransactionError;
use App\Http\Requests\TransactionUpdate;
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
     * @param TransactionUpdate $request
     * @param $id
	 *
     * @return Response
     */
    public function update(TransactionUpdate $request, $id)
    {
        if ($request->transactionRequestState === TransactionRequestStateEnum::REJECTED) {
            event(new TransactionFailed());
        }

        return new Response(200);
    }

    /**
     * @param TransactionError $request
     * @param $id
     */
    public function error(TransactionError $request, $id)
    {
        event(new TransactionFailed());

        return new Response(200);
    }
}
