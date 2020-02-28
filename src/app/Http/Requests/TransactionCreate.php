<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class TransactionCreate
 * @package App\Http\Requests
 *
 * @property string $currency
 * @property string $amount
 * @property string $type
 * @property string $subType
 * @property string $requestingOrganisationTransactionReference
 * @property string $descriptionText
 * @property string $geoCode
 * @property string $oneTimeCode
 * @property array $metadata
 * @property array $creditParty
 * @property array $debitParty
 * @property array $recipientKyc
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
        return [
            'amount'           => ['required', 'string', 'min:4', 'max:256', 'regex:\'^\d+\.\d{2}$\''],
            'currency'         => ['required', Rule::in(self::CURRENCY)],
            'type'             => ['required', Rule::in(self::TYPE)],
            'subType'          => 'string|min:0|max:256',
            'descriptionText'  => 'string|min:0|max:160',
            'requestingOrganisationTransactionReference' => 'string|min:0|max:256',
            'oneTimeCode'      => 'string|min:0|max:256',
            'geoCode'          => ['string', 'min:0', 'max:256', 'regex:\'^(-?(90|(\d|[1-8]\d)(\.\d{1,6}){0,1}))\,{1}(-?(180|(\d|\d\d|1[0-7]\d)(\.\d{1,6}){0,1}))$\''],
            'debitParty'       => 'required|array|max:1',
                'debitParty.*.key'    => 'required|string|min:1|max:256',
                'debitParty.*.value'  => 'required|string|min:1|max:256',
            'creditParty'      => 'required|array|max:1',
                'creditParty.*.key'   => 'required|string|min:1|max:256',
                'creditParty.*.value' => 'required|string|min:1|max:256',
            'recipientKyc'     => 'array',
                'recipientKyc.dateOfBirth' => 'string',
                'recipientKyc.subjectName' => 'array',
                    'recipientKyc.subjectName.fullName'   => 'string|min:0|max:256',
                    'recipientKyc.subjectName.firstName'  => 'string|min:0|max:256',
                    'recipientKyc.subjectName.middleName' => 'string|min:0|max:256',
                    'recipientKyc.subjectName.lastName'   => 'string|min:0|max:256',
            'metadata'         => 'array|max:1',
                'metadata.*.key'   => 'string|min:1|max:256',
                'metadata.*.value' => 'string|min:1|max:256',
        ];
    }

    /**
     * Data Mapping for Mojaloop POST transactionRequests
     *
     * @return array
     */
    public function mapInTo()
    {
        $amount = floatval($this->amount);
        $result = [
            'transactionRequestId' => Str::uuid(),
            'payee' => [
                'partyIdInfo'  => [
                    'partyIdType'     => strtoupper($this->creditParty[0]['key']),
                    'partyIdentifier' => $this->creditParty[0]['value'] != '16135551213' ? $this->creditParty[0]['value'] : null,
                ],
                'name' => $this->recipientKyc['subjectName']['fullName'] ?? '',
                'personalInfo' => [
                    'complexName' => [
                        'firstName'  => $this->recipientKyc['subjectName']['firstName'] ?? '',
                        'middleName' => $this->recipientKyc['subjectName']['middleName'] ?? '',
                        'lastName'   => $this->recipientKyc['subjectName']['lastName'] ?? '',
                    ],
                    'dateOfBirth' => $this->recipientKyc['dateOfBirth'] ?? '',
                ]
            ],
            'payer' => [
                'partyIdType'     => strtoupper($this->debitParty[0]['key']),
                'partyIdentifier' => $this->debitParty[0]['value'] != '16135551213' ? $this->debitParty[0]['value'] : null,
            ],
            'amount' => [
                'currency' => $this->currency,
                'amount'   => "$amount",
            ],
            'transactionType' => [
                'scenario'      => strtoupper($this->type),
                'subScenario'   => $this->subType ?? '',
                'initiator'     => 'PAYEE',
                'initiatorType' => 'BUSINESS',
                'refundInfo'    => [
                    'originalTransactionId' => $this->requestingOrganisationTransactionReference ?? '',
                ],
            ],
            'note'    => $this->descriptionText ?? '',
            'geoCode' => [
                'latitude'  => $this->geoCode ? explode(',', $this->geoCode)[0] : '',
                'longitude' => $this->geoCode ? explode(',', $this->geoCode)[1] : '',
            ],
            'authenticationType' => $this->oneTimeCode ? 'OTP' : '',
            'extensionList'      => [
                'extension' => [
                    [
                        'key'   => $this->metadata[0]['key'] ?? '',
                        'value' => $this->metadata[0]['value'] ?? '',
                    ],
                ],
            ],
        ];

        return $result;
    }
}

