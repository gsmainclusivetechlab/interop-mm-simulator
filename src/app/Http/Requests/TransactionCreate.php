<?php

namespace App\Http\Requests;

use App\Http\ValidationSets;
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
 * @property string $descriptionText
 * @property string $requestDate
 * @property string $requestingOrganisationTransactionReference
 * @property string $oneTimeCode
 * @property string $geoCode
 * @property string $originalTransactionReference
 * @property string $servicingIdentity
 * @property string $requestingLei
 * @property string $receivingLei
 * @property array $creditParty
 * @property array $debitParty
 * @property array $senderKyc
 * @property array $recipientKyc
 * @property array $fees
 * @property array $metadata
 * @property string $internationalTransferInformation
 * @property string $traceId
 */
class TransactionCreate extends FormRequest
{
    use ParseTraceId;

	/**
	 * Trace ID parsed from header traceparent
	 *
	 * @var string
	 */
	public $traceId;

	/**
	 * Mapping transaction type from SP to Mojaloop
	 */
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return Arr::crossJoin(
        	[
				'amount'           => ValidationSets::amount(),
				'currency'         => ValidationSets::currency(),
				'type'             => ValidationSets::type(),
				'subType'          => ValidationSets::standardString(),
				'descriptionText'  => ValidationSets::descriptionText(),
				'requestDate'      => ValidationSets::dateTime(),
				'requestingOrganisationTransactionReference' => ValidationSets::standardString(),
				'oneTimeCode'      => ValidationSets::standardString(),
				'geoCode'          => ValidationSets::geoCode(),
				'originalTransactionReference' => ValidationSets::standardString(),
				'servicingIdentity' => ValidationSets::standardString(),
				'requestingLei' => ValidationSets::lei(),
				'receivingLei' => ValidationSets::lei(),
			],
			ValidationSets::partyArray('debitParty'),
			ValidationSets::partyArray('creditParty'),
			ValidationSets::kyc('senderKyc'),
			ValidationSets::kyc('recipientKyc'),
			ValidationSets::feesArray('fees'),
			ValidationSets::metadataArray('metadata'),
			ValidationSets::internationalTransferInformation('internationalTransferInformation')
		);
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

	/**
	 * Configure the validator instance
	 *
	 * @param Validator $validator
	 */
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
                'x-callback-url.0.required' => __('Header X-Callback-URL is required!'),
                'x-callback-url.0.url' => __('Header X-Callback-URL has wrong format!'),
            ]);

            if ($headerValidator->fails()) {
                $validator->messages()->merge($headerValidator->messages());
                return;
            }

            $this->traceId = self::parseTraceId($this->headers->get('traceparent'));
        });
    }
}

