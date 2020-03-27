<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use App\Rules\Traceparent;
use App\Traits\ParseTraceId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
 * @property string $requestDate
 * @property string $geoCode
 * @property array $senderKyc
 * @property string $originalTransactionReference
 * @property string $servicingIdentity
 * @property string $transactionStatus
 * @property string $oneTimeCode
 * @property array $metadata
 * @property array $creditParty
 * @property array $debitParty
 * @property array $recipientKyc
 * @property string $transactionReceipt
 * @property string $traceId
 */
class TransactionCreate extends FormRequest
{
    use ParseTraceId;

    public $traceId;

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

    const GENDER = ['m', 'f', 'u'];

    const TYPE_MAP = [
        'billpay' => 'PAYMENT',
        'deposit' => 'DEPOSIT',
        'disbursement' => 'PAYMENT',
        'transfer' => 'TRANSFER',
        'merchantpay' => 'PAYMENT',
        'inttransfer' => 'TRANSFER',
        'adjustment' => 'TRANSFER',
        'reversal' => 'REFUND',
        'withdrawal' => 'WITHDRAWAL',
    ];

    /**c
     * Get the validation rules that apply to the request.
     * TODO debitParty creditParty and metadata array size
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount'           => ['required', 'regex:/^([0]|([1-9][0-9]{0,17}))([.][0-9]{0,3}[1-9])?$/'],
            'currency'         => ['required', Rule::in(self::CURRENCY)],
            'type'             => ['required', Rule::in(self::TYPE)],
            'subType'          => 'string|min:0|max:256',
            'descriptionText'  => 'string|min:0|max:160',
            'requestingOrganisationTransactionReference' => 'string|min:0|max:256',
            'oneTimeCode'      => 'string|min:0|max:256',
            'geoCode'          => [
                'string',
                'min:0',
                'max:256',
                'regex:\'^(-?(90|(\d|[1-8]\d)(\.\d{1,7}){0,1}))\,{1}\s?(-?(180|(\d|\d\d|1[0-7]\d)(\.\d{1,7}){0,1}))$\''
            ],
            'debitParty'       => 'required|array|max:1',
                'debitParty.*.key'    => 'required|string|min:1|max:256',
                'debitParty.*.value'  => 'required|string|min:1|max:256',
            'creditParty'      => 'required|array|max:1',
                'creditParty.*.key'   => 'required|string|min:1|max:256',
                'creditParty.*.value' => 'required|string|min:1|max:256',
            'senderKyc'     => 'array',
                'senderKyc.nationality' => 'regex:\'^[A-Z]{2}$\'',
                'senderKyc.dateOfBirth' => 'date:Y-m-d',
                'senderKyc.occupation' => 'string',
                'senderKyc.employerName' => 'string',
                'senderKyc.contactPhone' => 'regex:\'^\+?(?:\s*\d){6,15}$\'',
                'senderKyc.gender' => Rule::in(self::GENDER),
                'senderKyc.idDocument' => 'array',
                'senderKyc.postalAddress' => 'array',
                    'senderKyc.postalAddress.addressLine1' => 'string',
                    'senderKyc.postalAddress.addressLine2' => 'string',
                    'senderKyc.postalAddress.addressLine3' => 'string',
                    'senderKyc.postalAddress.city' => 'string',
                    'senderKyc.postalAddress.stateProvince' => 'string',
                    'senderKyc.postalAddress.postalCode' => 'string',
                    'senderKyc.postalAddress.country' => 'regex:\'^[A-Z]{2}$\'',
                'senderKyc.subjectName' => 'array',
                    'senderKyc.subjectName.title'      => 'string',
                    'senderKyc.subjectName.fullName'   => 'string|min:0|max:256',
                    'senderKyc.subjectName.firstName'  => 'string|min:0|max:256',
                    'senderKyc.subjectName.middleName' => 'string|min:0|max:256',
                    'senderKyc.subjectName.lastName'   => 'string|min:0|max:256',
                    'senderKyc.subjectName.nativeName' => 'string',
                'senderKyc.emailAddress' => 'string',
                'senderKyc.birthCountry' => 'regex:\'^[A-Z]{2}$\'',
            'recipientKyc'     => 'array',
                'recipientKyc.nationality' => 'regex:\'^[A-Z]{2}$\'',
                'recipientKyc.dateOfBirth' => 'date:Y-m-d',
                'recipientKyc.occupation' => 'string',
                'recipientKyc.employerName' => 'string',
                'recipientKyc.contactPhone' => 'regex:\'^\+?(?:\s*\d){6,15}$\'',
                'recipientKyc.gender' => Rule::in(self::GENDER),
                'recipientKyc.idDocument' => 'array',
                'recipientKyc.postalAddress' => 'array',
                    'recipientKyc.postalAddress.addressLine1' => 'string',
                    'recipientKyc.postalAddress.addressLine2' => 'string',
                    'recipientKyc.postalAddress.addressLine3' => 'string',
                    'recipientKyc.postalAddress.city' => 'string',
                    'recipientKyc.postalAddress.stateProvince' => 'string',
                    'recipientKyc.postalAddress.postalCode' => 'string',
                    'recipientKyc.postalAddress.country' => 'regex:\'^[A-Z]{2}$\'',
                'recipientKyc.subjectName' => 'array',
                    'recipientKyc.subjectName.title'      => 'string',
                    'recipientKyc.subjectName.fullName'   => 'string|min:0|max:256',
                    'recipientKyc.subjectName.firstName'  => 'string|min:0|max:256',
                    'recipientKyc.subjectName.middleName' => 'string|min:0|max:256',
                    'recipientKyc.subjectName.lastName'   => 'string|min:0|max:256',
                    'recipientKyc.subjectName.nativeName' => 'string',
                'recipientKyc.emailAddress' => 'string',
                'recipientKyc.birthCountry' => 'regex:\'^[A-Z]{2}$\'',
            'originalTransactionReference' => 'string|min:0|max:256',
            'servicingIdentity' => 'string|min:0|max:256',
            'transactionStatus' => Rule::in(Transaction::STATUSES),
            'transactionReceipt' => 'string|min:0|max:256',
            'requestDate' => 'date:Y-m-dTH:i:s.vZ',
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
    public function mapInTo(): array
    {
        $amount = floatval($this->amount);
        $result = [
            'transactionRequestId' => Str::uuid(),
            'payee' => [
                'partyIdInfo'  => [
                    'partyIdType'     => strtoupper($this->creditParty[0]['key']),
                    'partyIdentifier' => $this->creditParty[0]['value'] != '16135551213' ? $this->creditParty[0]['value'] : '',
                ],
            ],
            'payer' => [
                'partyIdType'     => strtoupper($this->debitParty[0]['key']),
                'partyIdentifier' => $this->debitParty[0]['value'] != '16135551213' ? $this->debitParty[0]['value'] : '',
            ],
            'amount' => [
                'currency' => $this->currency,
                'amount'   => "$amount",
            ],
            'transactionType' => [
                'scenario'      => self::TYPE_MAP[$this->type],
                'initiator'     => 'PAYEE',
                'initiatorType' => 'BUSINESS',
            ],
        ];

        if ($fullName = Arr::get($this->recipientKyc, 'subjectName.fullName')) {
            $result['payee']['name'] = $fullName;
        }

        if ($firstName = Arr::get($this->recipientKyc, 'subjectName.firstName')) {
            $result['payee']['personalInfo']['complexName']['firstName'] = $firstName;
        }

        if ($middleName = Arr::get($this->recipientKyc, 'subjectName.middleName')) {
            $result['payee']['personalInfo']['complexName']['middleName'] = $middleName;
        }

        if ($lastName = Arr::get($this->recipientKyc, 'subjectName.lastName')) {
            $result['payee']['personalInfo']['complexName']['lastName'] = $lastName;
        }

        if ($dateOfBirth = Arr::get($this->recipientKyc, 'dateOfBirth')) {
            $result['payee']['personalInfo']['dateOfBirth'] = $dateOfBirth;
        }

        if ($this->subType) {
            $result['transactionType']['subScenario'] = $this->subType;
        }

        if ($this->requestingOrganisationTransactionReference) {
            $result['transactionType']['refundInfo']['originalTransactionId'] = $this->requestingOrganisationTransactionReference;
        }

        if ($this->descriptionText) {
            $result['note'] = $this->descriptionText;
        }

        if ($this->geoCode) {
            $geoCodeParts = explode(',', $this->geoCode);

            $result['geoCode'] = [
                'latitude'  => $geoCodeParts[0],
                'longitude' => $geoCodeParts[1],
            ];
        }

        if ($this->oneTimeCode) {
            $result['authenticationType'] = 'OTP';
        }

        if ($this->metadata) {
            $result['extensionList']['extension'] = [
                [
                    'key'   => Arr::get($this->metadata, '0.key'),
                    'value' => Arr::get($this->metadata, '0.value'),
                ],
            ];
        }

        return $result;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            switch ($this->amount) {
                case '4.00':
                    throw new BadRequestHttpException();
                    break;
                case '4.01':
                    throw new UnauthorizedHttpException('');
                    break;
                case '4.04':
                    throw new NotFoundHttpException();
                    break;
                case '5.00':
                    throw new \Exception();
                    break;
                case '5.03':
                    throw new ServiceUnavailableHttpException();
                    break;
                default:
                    break;
            }

            $headerValidator = \Illuminate\Support\Facades\Validator::make($this->headers->all(), [
                'traceparent.0' => [
                    'required',
                    new Traceparent(),
                ],
                'x-callback-url.0' => [
                    'required',
                    'url',
                ]
            ], [
                'traceparent.0.required' => __('Header traceparent is required!'),
                'x-callback-url.0.required' => __('Header X-BaseCallback-URL is required!'),
                'x-callback-url.0.url' => __('Header X-BaseCallback-URL has wrong format!'),
            ]);

            if ($headerValidator->fails()) {
                $validator->messages()->merge($headerValidator->messages());
                return;
            }

            $this->traceId = self::parseTraceId($this->headers->get('traceparent'));
        });
    }
}

