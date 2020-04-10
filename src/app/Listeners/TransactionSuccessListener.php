<?php

namespace App\Listeners;

use App\Events\TransactionSuccess;
use App\Models\Transaction;
use App\OutgoingRequests\CallBacks\SuccessCallback;

class TransactionSuccessListener
{
    /**
     * Handle the event.
     *
     * @param  TransactionSuccess $event
     *
     * @return void
     */
    public function handle(TransactionSuccess $event)
    {
    	$transaction = Transaction::getCurrent();

    	$transaction->update(['transactionStatus' => $this->request->transferState ?? 'Completed']);

        if (!$transaction->callback_url) {
            return;
        }

		(new SuccessCallback($transaction))->send();
    }
}
