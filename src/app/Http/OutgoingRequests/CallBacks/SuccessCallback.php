<?php

namespace App\Requests\CallBacks;

use Illuminate\Support\Arr;

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

		$attributes = [
		    'subType',
            'descriptionText',
            'requestDate',
            'requestingOrganisationTransactionReference',
            'geoCode',
            'senderKyc',
            'recipientKyc',
            'originalTransactionReference',
            'servicingIdentity',
            'transactionReceipt',
            'metadata',
        ];

		foreach ($attributes as $attribute) {
            if ($value = $this->transaction->$attribute) {
                Arr::set($data, $attribute, $value);
            }
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
