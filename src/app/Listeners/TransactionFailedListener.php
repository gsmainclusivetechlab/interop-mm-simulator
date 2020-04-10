<?php

namespace App\Listeners;

use App\Events\TransactionFailed;
use App\Models\Transaction;
use App\OutgoingRequests\CallBacks\FailureCallback;

class TransactionFailedListener
{
	/**
     * Handle the event.
     *
     * @param  TransactionFailed $event
     *
     * @return void
     */
    public function handle(TransactionFailed $event)
    {
    	$transaction = Transaction::getCurrent();

    	$transaction->update(['transactionStatus' => $this->request->transferState ?? 'Failed']);

        if (!$transaction->callback_url) {
            return;
        }

		(new FailureCallback($transaction))->send();
    }
}
