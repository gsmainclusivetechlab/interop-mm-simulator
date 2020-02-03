<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
     * Postman collection example
     * @var string
     */
    public $postmanData = '{
        "transactionRequestId": "a46be97d-fc0a-4613-91c4-4115f6da10be",
        "payee": {
            "partyIdInfo": {
                "partyIdType": "PERSONAL_ID",
                "partyIdentifier": "16135551212",
                "partySubIdOrType": "DRIVING_LICENSE",
                "fspId": "1234"
            },
            "merchantClassificationCode": "4321",
            "name": "Justin Trudeau",
            "personalInfo": {
                "complexName": {
                    "firstName": "Justin",
                    "middleName": "Pierre",
                    "lastName": "Trudeau"
                },
                "dateOfBirth": "1971-12-25"
            }
        },
        "payer": {
            "partyIdType": "PERSONAL_ID",
            "partyIdentifier": "16135551212",
            "partySubIdOrType": "DRIVING_LICENSE",
            "fspId": "1234"
        },
        "amount": {
            "currency": "USD",
            "amount": "123.45"
        },
        "transactionType": {
            "scenario": "DEPOSIT",
            "subScenario": "locally defined sub-scenario",
            "initiator": "PAYEE",
            "initiatorType": "CONSUMER",
            "refundInfo": {
                "originalTransactionId": "a46be97d-fc0a-4613-91c4-4115f6da10be",
                "refundReason": "free text indicating reason for the refund"
            },
            "balanceOfPayments": "123"
        },
        "note": "Free-text memo",
        "geoCode": {
            "latitude": "+45.4215",
            "longitude": "+75.6972"
        },
        "authenticationType": "OTP",
        "expiration": "2016-05-24T08:38:08.699-04:00",
        "extensionList": {
            "extension": [
                {
                    "key": "errorDescription",
                    "value": "This is a more detailed error description"
                },
                {
                    "key": "errorDescription",
                    "value": "This is a more detailed error description"
                }
            ]
        }
    }';

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
    public function dataMapping()
    {
        $id = rand(1000000, 9999999);
        $result = [
//            'transactionRequestId' => "249d49c9-c804-4d0b-801d-663edc6dbae2",
            'transactionRequestId' => "$id",
            'payee' => [
//                'partyIdInfo' => [
//                    'partyIdType' => strtoupper($this->creditParty[0]['key']),
//                    'partyIdentifier' => $this->creditParty[0]['value'],
//                ],

                // testing
                'partyIdInfo' => [
                    'partyIdType' => 'PERSONAL_ID',
                    'partyIdentifier' => '16135551212',
                    'partySubIdOrType' => 'DRIVING_LICENSE',
                    'fspId' => '1234',
                ],
                'merchantClassificationCode' => '4321',
                'name' => 'Justin Trudeau',
                'personalInfo' => [
                    'complexName' => [
                        'firstName' => 'Justin',
                        'middleName' => 'Pierre',
                        'lastName' => 'Trudeau'
                    ],
                    'dateOfBirth' => '1971-12-25'
                ]
            ],
            'payer' => [
//                'partyIdType' => strtoupper($this->debitParty[0]['key']),
//                'partyIdentifier' => $this->debitParty[0]['value'],

            // testing
                'partyIdType' => 'PERSONAL_ID',
                'partyIdentifier' => '16135551212',
                'partySubIdOrType' => 'DRIVING_LICENSE',
                'fspId' => '1234'
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
                    ['key' => $this->metadata[0]['key'],
                    'value' => $this->metadata[0]['value']]
                ]
            ],
        ];

        return $result;
    }
}

