<?php

namespace App\Http\Requests;

use App\Http\ValidationSets;
use App\Traits\ParseTraceId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

/**
 * Class QuotationsCreate
 * @package App\Http\Requests
 *
 * @property string $requestDate
 * @property string $requestCurrency
 * @property string $requestAmount
 * @property string $type
 * @property array $creditParty
 * @property array $debitParty
 * @property array $metadata
 */
class QuotationsCreate extends FormRequest
{
    use ParseTraceId;

    /**
     * Trace ID parsed from header traceparent
     *
     * @var string
     */
    public $traceId;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
            [
                'requestDate' => ValidationSets::requiredString(),
                'requestAmount' => [
                    'required',
                    ValidationSets::amount()
                ],
                'requestCurrency' => [
                    'required',
                    ValidationSets::currencyMmo()
                ],
                'subType' => ValidationSets::standardString(),
                'chosenDeliveryMethod' => ValidationSets::standardString(),
                'quotes'  => 'array',
                    'quotes.*.quoteId' => 'string',
                    'quotes.*.quoteExpiryTime' => 'string',
                    'quotes.*.receivingServiceProvider' => 'string',
                    'quotes.*.sendingAmount' => 'string',
                    'quotes.*.sendingCurrency' => 'string',
                    'quotes.*.receivingAmount' => 'string',
                    'quotes.*.receivingCurrency' => 'string',
                    'quotes.*.fxRate' => 'string',
                    'quotes.*.deliveryMethod' => 'string',
                    'quotes.*.fees' => 'array',
                        'quotes.*.fees.*.feeType'     => 'string',
                        'quotes.*.fees.*.feeAmount'   => 'string',
                        'quotes.*.fees.*.feeCurrency' => 'string',

                'senderBlockingReason' => ValidationSets::standardString(),
                'recipientBlockingReason' => ValidationSets::standardString(),
            ],
            ValidationSets::partyArray('debitParty'),
            ValidationSets::partyArray('creditParty'),
            ValidationSets::kyc('senderKyc'),
            ValidationSets::kyc('recipientKyc'),
            ValidationSets::metadataArray('metadata'),
        );
    }

    /**
     * payer -> debit
     * payee -> credit
     *
     * @return array
     */
    public function mapInTo()
    {
        $amount = floatval($this->requestAmount);
        return [
            'quoteId' => Str::uuid(),
            'transactionId' => Str::uuid(),
            'payer' => [
                'partyIdInfo' => [
                    'partyIdType' => strtoupper($this->debitParty[0]['key']),
                    'partyIdentifier' => $this->debitParty[0]['value'],
                ],
            ],
            'payee' => [
                'partyIdInfo' => [
                    'partyIdType' => strtoupper($this->creditParty[0]['key']),
                    'partyIdentifier' => $this->creditParty[0]['value'],
                ]
            ],
            'amountType' => 'SEND',
            'amount' => [
                'amount' => "$amount",
                'currency' => $this->requestCurrency,
            ],
            'transactionType' => [
                'scenario' => 'TRANSFER',
                'initiator' => 'PAYER',
                'initiatorType' => 'CONSUMER'
            ],
        ];
    }
}

