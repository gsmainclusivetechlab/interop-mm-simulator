<?php

namespace App\Http\Requests;

use App\Http\ValidationSets;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

/**
 * Class QuoteCreate
 * @package App\Http\Requests
 *
 * @property string $quoteId
 * @property string $transactionId
 * @property string $transactionRequestId
 * @property array $amount
 */
class QuoteCreate extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
				[
				'quoteId'              => [
					'required',
					ValidationSets::correlationId(),
				],
				'transactionId'        => [
					'required',
					ValidationSets::correlationId(),
				],
				'transactionRequestId' => ValidationSets::correlationId(),
				'payee'                => 'required|array',
				'payer'                => 'required|array',
				'amountType'           => ValidationSets::amountType(),
				'amount'               => 'required|array',
				'fees'                 => 'array',
				'transactionType'      => 'required',
				'note' => ValidationSets::note(),
				'expiration' => ValidationSets::dateTime(),
				'extensionList' => ValidationSets::extensionList('extensionList'),
			],
			ValidationSets::partyMojaloop('payee'),
			ValidationSets::partyMojaloop('payer'),
			ValidationSets::money('amount'),
			ValidationSets::money('fees'),
			ValidationSets::transactionType('transactionType'),
			ValidationSets::geoCodeMoja('geoCode')
		);
    }

    /**
     * payer -> debit
     * payee -> credit
     *
     * @return array
     * @throws \Exception
     */
    public function mapInTo()
    {
        return [
            'transferAmount' => [
                'amount'   => $this->amount['amount'],
                'currency' => $this->amount['currency'],
            ],
            'expiration'     => (new Carbon())->addSeconds(600000)->toIso8601ZuluString('millisecond'),
            'ilpPacket'      => 'AQAAAAAAAADIEHByaXZhdGUucGF5ZWVmc3CCAiB7InRyYW5zYWN0aW9uSWQiOiIyZGY3NzRlMi1mMWRiLTRmZjctYTQ5NS0yZGRkMzdhZjdjMmMiLCJxdW90ZUlkIjoiMDNhNjA1NTAtNmYyZi00NTU2LThlMDQtMDcwM2UzOWI4N2ZmIiwicGF5ZWUiOnsicGFydHlJZEluZm8iOnsicGFydHlJZFR5cGUiOiJNU0lTRE4iLCJwYXJ0eUlkZW50aWZpZXIiOiIyNzcxMzgwMzkxMyIsImZzcElkIjoicGF5ZWVmc3AifSwicGVyc29uYWxJbmZvIjp7ImNvbXBsZXhOYW1lIjp7fX19LCJwYXllciI6eyJwYXJ0eUlkSW5mbyI6eyJwYXJ0eUlkVHlwZSI6Ik1TSVNETiIsInBhcnR5SWRlbnRpZmllciI6IjI3NzEzODAzOTExIiwiZnNwSWQiOiJwYXllcmZzcCJ9LCJwZXJzb25hbEluZm8iOnsiY29tcGxleE5hbWUiOnt9fX0sImFtb3VudCI6eyJjdXJyZW5jeSI6IlVTRCIsImFtb3VudCI6IjIwMCJ9LCJ0cmFuc2FjdGlvblR5cGUiOnsic2NlbmFyaW8iOiJERVBPU0lUIiwic3ViU2NlbmFyaW8iOiJERVBPU0lUIiwiaW5pdGlhdG9yIjoiUEFZRVIiLCJpbml0aWF0b3JUeXBlIjoiQ09OU1VNRVIiLCJyZWZ1bmRJbmZvIjp7fX19',
            'condition'      => 'HOr22-H3AfTDHrSkPjJtVPRdKouuMkDXTR4ejlQa8Ks',
        ];
    }
}

