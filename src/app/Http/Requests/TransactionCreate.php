<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * @property string $currency
 * @property string $amount
 * @property string $type
 * @property string $subType
 * @property string $requestingOrganisationTransactionReference
 * @property string $descriptionText
 * @property string $geoCode
 * @property array $metadata
 * @property array $creditParty
 * @property array $debitParty
 */
class TransactionCreate extends FormRequest
{
    /**
     * Available currency
     */
    const CURRENCY = ['AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BHD', 'BIF',
        'BMD', 'BND', 'BOB', 'BOV', 'BRL', 'BSD', 'BTN', 'BWP', 'BYR', 'BZD', 'CAD', 'CDF', 'CHE', 'CHF', 'CHW', 'CLF', 'CLP', 'CNY',
        'COP', 'COU', 'CRC', 'CUC', 'CUP', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD', 'EGP', 'ERN', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP',
        'GEL', 'GHS', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'IQD', 'IRR', 'ISK',
        'JMD', 'JOD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KPW', 'KRW', 'KWD', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'LYD',
        'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MXV', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO',
        'NOK', 'NPR', 'NZD', 'OMR', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD',
        'SCR', 'SDG', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'SSP', 'STD', 'SVC', 'SYP', 'SZL', 'THB', 'TJS', 'TMT', 'TND', 'TOP',
        'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'USD', 'USN', 'UYI', 'UYU', 'UZS', 'VEF', 'VND', 'VUV', 'WST', 'XAF', 'XAG', 'XAU',
        'XBA', 'XBB', 'XBC', 'XBD', 'XCD', 'XDR', 'XOF', 'XPD', 'XPF', 'XPT', 'XSU', 'XTS', 'XUA', 'XXX', 'YER', 'ZAR', 'ZMW', 'ZWL'];

    /**
     * Available scenario type
     */
    const TYPE = [
        'billpay', 'deposit', 'disbursement', 'transfer', 'merchantpay', 'inttransfer', 'adjustment', 'reversal', 'withdrawal',
    ];

    /**
     * Get the validation rules that apply to the request.
     * TODO debitParty creditParty and metadata array size
     * @return array
     */
    public function rules()
    {
        return [                                                                              //// transform to mojaloop
            'amount'           => 'required|string|min:4|max:256|regex:\'^\d+\.\d{2}$\'',     //// transform to amount['amount'=>]
            'currency'         => ['required', Rule::in(self::CURRENCY)],              //// transform to amount['currency'=>]
            'type'             => ['required', Rule::in(self::TYPE)],                  //// transform to transactionType['scenario'=>]
            ///
            'subType'          => 'string|min:0|max:256',                                     //// to transactionType['subScenario'=>]
            'descriptionText'  => 'string|min:0|max:160',                                     //// to `note`
            'requestDate'      => '',                 // no need

            'requestingOrganisationTransactionReference' => 'required|string|min:0|max:256',  //// transform to transactionType['refundInfo'=>['originalTransactionId'=>]]
            ///
            'oneTimeCode'      => 'string|min:0|max:256',                                     ///// authenticationType = 'OTP'
            'geoCode'          => ['required', 'string', 'min:0', 'max:256', 'regex:\'^(-?(90|(\d|[1-8]\d)(\.\d{1,6}){0,1}))\,{1}(-?(180|(\d|\d\d|1[0-7]\d)(\.\d{1,6}){0,1}))$\''],  //// geoCode['latitude'=>,'longitude'=>]
            'debitParty'       => 'required|array|max:1',                                           //// transform to payer['partyIdType'=>,'partyIdentifier'=>]
                'debitParty.*.key'    => 'required|string|min:1|max:256',                              //// transform to payer['partyIdType'=>]
                'debitParty.*.value'  => 'required|string|min:1|max:256',                              //// transform to payer['partyIdentifier'=>]
            'creditParty'      => 'required|array|max:1',                                           //// transform to payee['partyIdInfo'=>['partyIdType'=>,'partyIdentifier'=>]]
                'creditParty.*.key'   => 'required|string|min:1|max:256',                              //// transform to payee['partyIdInfo'=>['partyIdType'=>]]
                'creditParty.*.value' => 'required|string|min:1|max:256',                              //// transform to payee['partyIdInfo'=>['partyIdentifier'=>]]
            ///
            'senderKyc'        => '',                 // no need
            'recipientKyc'     => '',                                                         //// transform to payee['name'=>,'personalInfo'=>['complexName'=>,'dateOfBirth'=>]]
            'originalTransactionReference'=> '',      // no need
            'servicingIdentity'=> '',                 // no need
            'fees'             => '',                 // no need
            'requestingLei'    => '',                 // no need
            'receivingLei'     => '',                 // no need
            'metadata'         => 'required|array|max:1',                                           ////  extensionList['extension'=>['key'=>,'value'=>]]
                'metadata.*.key'   => 'required|string|min:1|max:256',                              //// transform to extensionList['extension'=>['key'=>]]
                'metadata.*.value' => 'required|string|min:1|max:256',                              //// transform to extensionList['extension'=>['value'=>]]
            'transactionStatus'=> '',                 // ?
            'internationalTransferInformation' => '', // no need
        ];
    }

    /**
     * Data Mapping for Mojaloop POST transactionRequests
     *
     * @return array
     */
    public function mapInTo()
    {
        $result = [
            'transactionRequestId' => Str::uuid(),
            'payee' => [
                'partyIdInfo' => [
                    'partyIdType' => strtoupper($this->creditParty[0]['key']),
                    'partyIdentifier' => $this->creditParty[0]['value'],
                ],
            ],
            'payer' => [
                'partyIdType' => strtoupper($this->debitParty[0]['key']),
                'partyIdentifier' => $this->debitParty[0]['value'],
            ],
            'amount' => [
                'currency' => $this->currency,
                'amount' => $this->amount,
            ],
            'transactionType' => [
                'scenario' => strtoupper($this->type),
//                'subScenario' => $this->subType, //
                'initiator' => 'PAYEE',
                'initiatorType' => 'BUSINESS',
                'refundInfo' => [
                    'originalTransactionId' => $this->requestingOrganisationTransactionReference,
//                    'refundReason' => 'reason', //
                ],
//                'balanceOfPayments' => 'balance' //
            ],
//            'note' => $this->descriptionText, //
            'geoCode' => [
                'latitude' => explode(',', $this->geoCode)[0],
                'longitude' => explode(',', $this->geoCode)[1],
            ],
            'authenticationType' => 'OTP',
//            'expiration' => 'exp', //
            'extensionList' => [
                'extension' => [
                    [
                        'key' => $this->metadata[0]['key'],
                        'value' => $this->metadata[0]['value'],
                    ],
                ],
            ],
        ];

        return $result;
    }
}

