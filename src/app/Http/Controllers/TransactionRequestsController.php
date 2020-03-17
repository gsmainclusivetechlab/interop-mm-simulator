<?php


namespace App\Http\Controllers;

use App\Events\TerminateTransaction;
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
        if ($request->transactionRequestState === 'rejected') {
            $transaction = Transaction::getCurrent();

            $transaction->update(['transactionStatus' => 'REJECTED']);

            event(new TerminateTransaction($transaction));
        }

        return new Response(200);
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
        $transaction = Transaction::getCurrent();

        $transaction->update(['transactionStatus' => 'REJECTED']);

        event(new TerminateTransaction($transaction));
    }
}
