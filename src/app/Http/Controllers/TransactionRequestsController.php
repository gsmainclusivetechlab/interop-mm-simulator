<?php


namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequestsUpdate;
use Illuminate\Support\Facades\Log;

/**
 * Mojaloop Request controller
 *
 * Class TransactionRequestsController
 * @package App\Http\Controllers
 */
class TransactionRequestsController extends Controller
{
    public function update(TransactionRequestsUpdate $request, $id)
    {
        Log::info(
            'PUT /transactionRequest:' . PHP_EOL
            . $request->getContent() . PHP_EOL
            . 'id = ' . $id . PHP_EOL
        );
    }
}
