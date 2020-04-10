<?php

namespace App\OutgoingRequests\CallBacks;

class SuccessCallback extends BaseCallback
{
	protected function collectData(): array
	{
		$data = [
			'amount' => $this->transaction->amount,
            'currency' => $this->transaction->currency,
            'type' => $this->transaction->type,
            'debitParty' => $this->transaction->debitParty,
            'creditParty' => $this->transaction->creditParty,
            'transactionStatus' => $this->transaction->transactionStatus,
            'transactionReference' => '',
		];

        if ($this->transaction->subType) {
            $data['subType'] = $this->transaction->subType;
        }

        if ($this->transaction->descriptionText) {
            $data['descriptionText'] = $this->transaction->descriptionText;
        }

        if ($this->transaction->requestDate) {
            $data['requestDate'] = $this->transaction->requestDate;
        }

        if ($this->transaction->requestingOrganisationTransactionReference) {
            $data['requestingOrganisationTransactionReference'] = $this->transaction->requestingOrganisationTransactionReference;
        }

        if ($this->transaction->geoCode) {
            $data['geoCode'] = $this->transaction->geoCode;
        }

        if ($this->transaction->senderKyc) {
            $data['senderKyc'] = $this->transaction->senderKyc;
        }

        if ($this->transaction->recipientKyc) {
            $data['recipientKyc'] = $this->transaction->recipientKyc;
        }

        if ($this->transaction->originalTransactionReference) {
            $data['originalTransactionReference'] = $this->transaction->originalTransactionReference;
        }

        if ($this->transaction->servicingIdentity) {
            $data['servicingIdentity'] = $this->transaction->servicingIdentity;
        }

        if ($this->transaction->transactionReceipt) {
            $data['transactionReceipt'] = $this->transaction->transactionReceipt;
        }

        if ($this->transaction->metadata) {
            $data['metadata'] = $this->transaction->metadata;
        }

		return $data;
	}

	protected function collectHeaders(): array
	{
		return [
			'traceparent' => request()->header('traceparent'),
		];
	}
}
