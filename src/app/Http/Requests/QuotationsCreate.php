<?php

namespace App\Http\Requests;

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
        return [
            'requestDate' => 'string|required',                              // !
            'debitParty' => 'required|array|max:1',
                'debitParty.*.key' => 'required|string|min:1|max:256',
                'debitParty.*.value' => 'required|string|min:1|max:256',
            'creditParty' => 'required|array|max:1',                         // !
                'creditParty.*.key' => 'required|string|min:1|max:256',
                'creditParty.*.value' => 'required|string|min:1|max:256',

            'senderKyc' => 'array',
                'senderKyc.nationality' => 'string',
                'senderKyc.dateOfBirth' => 'string',
                'senderKyc.occupation' => 'string',
                'senderKyc.employerName' => 'string',
                'senderKyc.contactPhone' => 'string',
                'senderKyc.gender' => 'string',
                'senderKyc.idDocument'=> 'array',
                    'senderKyc.idDocument.*.idType' => 'string',
                    'senderKyc.idDocument.*.idNumber' => 'string',
                    'senderKyc.idDocument.*.issueDate' => 'string',
                    'senderKyc.idDocument.*.expiryDate' => 'string',
                    'senderKyc.idDocument.*.issuer' => 'string',
                    'senderKyc.idDocument.*.issuerPlace' => 'string',
                    'senderKyc.idDocument.*.issuerCountry' => 'string',
                    'senderKyc.idDocument.*.otherIdDescription' => 'string',
                'senderKyc.postalAddress' => 'array',
                    'senderKyc.postalAddress.addressLine1' => 'string',
                    'senderKyc.postalAddress.addressLine2' => 'string',
                    'senderKyc.postalAddress.addressLine3' => 'string',
                    'senderKyc.postalAddress.city' => 'string',
                    'senderKyc.postalAddress.stateProvince' => 'string',
                    'senderKyc.postalAddress.postalCode' => 'string',
                    'senderKyc.postalAddress.country' => 'string',
                'senderKyc.subjectName' => 'array',
                    'senderKyc.subjectName.title' => 'string',
                    'senderKyc.subjectName.firstName' => 'string',
                    'senderKyc.subjectName.middleName' => 'string',
                    'senderKyc.subjectName.lastName' => 'string',
                    'senderKyc.subjectName.fullName' => 'string',
                    'senderKyc.subjectName.nativeName' => 'string',
                'senderKyc.emailAddress' => 'string',
                'senderKyc.birthCountry' => 'string',

            'recipientKyc' => 'array',
                'recipientKyc.nationality' => 'string',
                'recipientKyc.dateOfBirth' => 'string',
                'recipientKyc.occupation' => 'string',
                'recipientKyc.employerName' => 'string',
                'recipientKyc.contactPhone' => 'string',
                'recipientKyc.gender' => 'string',
                'recipientKyc.idDocument' => 'array',
                    'recipientKyc.idDocument.*.idType' => 'string',
                    'recipientKyc.idDocument.*.idNumber' => 'string',
                    'recipientKyc.idDocument.*.issueDate' => 'string',
                    'recipientKyc.idDocument.*.expiryDate' => 'string',
                    'recipientKyc.idDocument.*.issuer' => 'string',
                    'recipientKyc.idDocument.*.issuerPlace' => 'string',
                    'recipientKyc.idDocument.*.issuerCountry' => 'string',
                    'recipientKyc.idDocument.*.otherIdDescription' => 'string',
                'recipientKyc.postalAddress' => 'array',
                    'recipientKyc.postalAddress.addressLine1' => 'string',
                    'recipientKyc.postalAddress.addressLine2' => 'string',
                    'recipientKyc.postalAddress.addressLine3' => 'string',
                    'recipientKyc.postalAddress.city' => 'string',
                    'recipientKyc.postalAddress.stateProvince' => 'string',
                    'recipientKyc.postalAddress.postalCode' => 'string',
                    'recipientKyc.postalAddress.country' => 'string',
                'recipientKyc.subjectName' => 'array',
                    'recipientKyc.subjectName.title' => 'string',
                    'recipientKyc.subjectName.firstName' => 'string',
                    'recipientKyc.subjectName.middleName' => 'string',
                    'recipientKyc.subjectName.lastName' => 'string',
                    'recipientKyc.subjectName.fullName' => 'string',
                    'recipientKyc.subjectName.nativeName' => 'string',
                'recipientKyc.emailAddress' => 'string',
                'recipientKyc.birthCountry' => 'string',

            'requestAmount' => 'required',                                   // !
            'requestCurrency' => 'required',                                 // !
            'subType' => 'string',
            'chosenDeliveryMethod' => 'string',
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

            'senderBlockingReason' => 'string',
            'recipientBlockingReason' => 'string',
            'metadata' => 'array',
                'metadata.*.key'    => 'string',
                'metadata.*.value'  => 'string',
        ];
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
//                    'fspId' => 'payerfsp'
                ],
            ],
            'payee' => [
                'partyIdInfo' => [
                    'partyIdType' => strtoupper($this->creditParty[0]['key']),
                    'partyIdentifier' => $this->creditParty[0]['value'],
//                    'fspId' => 'payeefsp'
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

