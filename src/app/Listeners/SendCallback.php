<?php

namespace App\Listeners;

use App\Events\TerminateTransaction;
use App\Models\Transaction;
use App\Requests\Callback;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SendCallback
{
    private $request;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  TerminateTransaction  $event
     * @return void
     */
    public function handle(TerminateTransaction $event)
    {
        if (!$event->transaction || !$event->transaction->callback_url) {
            return;
        }

        $headers['traceparent'] = request()->header('traceparent');

        $data = [
            'amount' => Arr::get($this->request->amount, 'amount'),
            'currency' => Arr::get($this->request->amount, 'currency'),
            'type' => $event->transaction->type,
            'debitParty' => $event->transaction->debitParty,
            'creditParty' => $event->transaction->creditParty,
            'transactionStatus' => $event->transaction->transactionStatus,
        ];



        if ($event->transaction->subType) {
            $data['subType'] = $event->transaction->subType;
        }

        if ($event->transaction->descriptionText) {
            $data['descriptionText'] = $event->transaction->descriptionText;
        }

        if ($event->transaction->requestDate) {
            $data['requestDate'] = $event->transaction->requestDate;
        }

        if ($event->transaction->requestingOrganisationTransactionReference) {
            $data['requestingOrganisationTransactionReference'] = $event->transaction->requestingOrganisationTransactionReference;
        }

        if ($event->transaction->geoCode) {
            $data['geoCode'] = $event->transaction->geoCode;
        }

        if ($event->transaction->senderKyc) {
            $data['senderKyc'] = $event->transaction->senderKyc;
        }

        if ($event->transaction->recipientKyc) {
            $data['recipientKyc'] = $event->transaction->recipientKyc;
        }

        if ($event->transaction->originalTransactionReference) {
            $data['originalTransactionReference'] = $event->transaction->originalTransactionReference;
        }

        if ($event->transaction->servicingIdentity) {
            $data['servicingIdentity'] = $event->transaction->servicingIdentity;
        }

        if ($event->transaction->transactionReceipt) {
            $data['transactionReceipt'] = $event->transaction->transactionReceipt;
        }

        if ($event->transaction->metadata) {
            $data['metadata'] = $event->transaction->metadata;
        }

        (new Callback($data, $headers, $event->transaction->callback_url))->send();
    }
}
