<?php

namespace App\Http;

use App\Enums\AmountTypeEnum;
use App\Enums\CurrencyMmoEnum;
use App\Enums\CurrencyMojaEnum;
use App\Enums\DeliveryMethodEnum;
use App\Enums\GenderEnum;
use App\Enums\IdTypeEnum;
use App\Enums\NationalityEnum;
use App\Enums\PartyIdTypeEnum;
use App\Enums\TransactionInitiatorEnum;
use App\Enums\TransactionInitiatorTypeEnum;
use App\Enums\TransactionRequestStateEnum;
use App\Enums\TransactionScenarioEnum;
use App\Enums\TypeEnum;
use BenSampo\Enum\Rules\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Static_;

/**
 * Frequently used Validation Sets
 *
 * @package App\Http
 */
class ValidationSets
{

	/**
	 * Standard string
	 *
	 * @return string
	 */
	public static function standardString(): string
	{
		return 'string|max:256|nullable';
	}

	/**
	 * Required standard string
	 *
	 * @return string
	 */
	public static function requiredString(): string
	{
		return 'required|string|max:256';
	}

	/**
	 * DateTime string
	 *
	 * @return string
	 */
	public static function dateTime(): string
	{
		return 'date:Y-m-dTH:i:s.vZ';
	}

	/**
	 * Date string
	 *
	 * @return string
	 */
	public static function date(): string
	{
		return 'date:Y-m-d';
	}

	/**
	 * Amount
	 *
	 * @return array
	 */
	public static function amount(): string
	{
		return 'regex:/^([0]|([1-9][0-9]{0,17}))([.][0-9]{0,3}[1-9])?$/';
	}

	/**
	 * Currency
	 *
	 * @return array
	 */
	public static function currencyMmo(): EnumValue
	{
		return new EnumValue(CurrencyMmoEnum::class);
	}

	/**
	 * The harmonised Transaction Type
	 *
	 * @return array
	 */
	public static function type(): array
	{
		return [
			'required',
			new EnumValue(TypeEnum::class),
		];
	}

	/**
	 * A collection of key/value pairs that enable the party to be identified
	 *
	 * @param string $field
	 *
	 * @return array
	 */
	public static function partyArray(string $field): array
	{
		$rules = array_merge(
			[
				$field => 'required|array|max:10',
			],
			self::partyMMO($field)
		);

		return $rules;
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function partyMMO(string $field): array
	{
		return [
			$field . '.*.key' => self::requiredString(),
			$field . '.*.value' => self::requiredString(),
		];
	}

	/**
	 * Free format text description of the transaction provided by the client. This can be provided as a reference
	 * for the receiver on a notification SMS and on an account statement
	 *
	 * @return string
	 */
	public static function descriptionText(): string
	{
		return 'string|max:160|nullable';
	}

	/**
	 * Indicates the geographic location from where the transaction was initiated
	 *
	 * @return array
	 */
	public static function geoCodeMmo(): array
	{
		return [
			'nullable',
			'regex: /^(-?(90|(\d|[1-8]\d)(\.\d{1,6}){0,1}))\,{1}(-?(180|(\d|\d\d|1[0-7]\d)(\.\d{1,6}){0,1}))$/',
		];
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function kyc(string $field): array
	{
		return array_merge(
			[
				$field => 'array',
				$field . '.' . 'nationality' => self::nationality(),
				$field . '.' . 'dateOfBirth' => self::date(),
				$field . '.' . 'occupation' => self::standardString(),
				$field . '.' . 'employerName' => self::standardString(),
				$field . '.' . 'contactPhone' => self::standardString(),
				$field . '.' . 'gender' => new EnumValue(GenderEnum::class),
				$field . '.' . 'emailAddress' => self::standardString(),
				$field . '.' . 'birthCountry' => self::nationality(),
			],
			self::idDocumentArray($field . '.idDocument'),
			self::postalAddress($field . '.postalAddress'),
			self::subjectName($field . '.subjectName')
		);
	}

	/**
	 * @return EnumValue
	 */
	public static function nationality(): EnumValue
	{
		return new EnumValue(NationalityEnum::class);
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function idDocumentArray(string $field): array
	{
		return array_merge(
			[
				$field => 'array|max:10',
			],
			self::idDocument($field)
		);
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function idDocument(string $field): array
	{
		return [
			$field . '.*.idType' => [
				'required_with:' . $field,
				new EnumValue(IdTypeEnum::class),
			],
			$field . '.*.idNumber' => self::standardString(),
			$field . '.*.issueDate' => self::date(),
			$field . '.*.expiryDate' => self::date(),
			$field . '.*.issuer' => self::standardString(),
			$field . '.*.issuerPlace' => self::standardString(),
			$field . '.*.issuerCountry' => self::nationality(),
			$field . '.*.otherIddescription' => self::standardString(),
		];
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function postalAddress(string $field): array
	{
		return [
			$field => 'array',
			$field . '.addressLine1' => self::standardString(),
			$field . '.addressLine2' => self::standardString(),
			$field . '.addressLine3' => self::standardString(),
			$field . '.city' => self::standardString(),
			$field . '.stateProvince' => self::standardString(),
			$field . '.postalCode' => self::standardString(),
			$field . '.country' => [
				'required_with:' . $field,
				self::nationality(),
			],
		];
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function subjectName(string $field): array
	{
		return [
			$field => 'array',
			$field . '.title' => self::standardString(),
			$field . '.fullName' => self::standardString(),
			$field . '.firstName' => self::standardString(),
			$field . '.middleName' => self::standardString(),
			$field . '.lastName' => self::standardString(),
			$field . '.nativeName' => self::standardString(),
		];
	}

	/**
	 * Returns all fees that are applicable to the quote
	 *
	 * @param string $field
	 *
	 * @return array
	 */
	public static function feesArray(string $field): array
	{
		return array_merge(
			[
				$field => 'array|max:20',
			],
			self::fees($field)
		);
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function fees(string $field): array
	{
		return [
			$field . '.*.feeType' => self::requiredString(),
			$field . '.*.feeAmount' => self::amount(),
			$field . '.*.feeCurrency' => self::currencyMmo(),
		];
	}

	/**
	 * Legal Entity Identifier of the organisation that is requesting/receiving the transaction
	 *
	 * @return string
	 */
	public static function lei(): string
	{
		return 'regex: /^[A-Z0-9]{4}00[A-Z0-9]{12}\\d{2}$/';
	}

	/**
	 * A collection of key/value pairs. These can be used to populate additional object
	 *
	 * @param string $field
	 *
	 * @return array
	 */
	public static function metadataArray(string $field): array
	{
		return array_merge(
			[
				$field => 'array|max:20',
			],
			self::metadata($field)
		);
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function metadata(string $field): array
	{
		return [
			$field . '.*.key' => self::requiredString(),
			$field . '.*.value' => self::requiredString(),
		];
	}

	/**
	 * A collection of properties detailing information specifically used for international transfers
	 *
	 * @param string $field
	 *
	 * @return array
	 */
	public static function internationalTransferInformation(string $field): array
	{
		return [
			$field => 'array',
			$field . '.originCountry' => [
				'required_with:' . $field,
				self::nationality(),
			],
			$field . '.quotationReference' => self::standardString(),
			$field . '.quoteId' => self::standardString(),
			$field . '.receivingCountry' => self::nationality(),
			$field . '.remittancePurpose' => self::standardString(),
			$field . '.relationshipSender' => self::standardString(),
			$field . '.deliveryMethod' => new EnumValue(DeliveryMethodEnum::class),
		];
	}

	public static function correlationId(): string
	{
		return 'regex: /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
	}

	public static function transactiornRequestState(): EnumValue
	{
		return new EnumValue(TransactionRequestStateEnum::class);
	}

	public static function extensionList(string $field): array
	{
		return array_merge([
			$field => 'array',
			$field . '.extension' => [
				'required_with:' . $field,
				'array',
				'max:16',
			],
		], self::extension($field . '.extension'));
	}

	public static function extension(string $field): array
	{
		return [
			$field . '.key' => self::extensionKey($field),
			$field . '.value' => self::extensionValue($field),
		];
	}

	public static function extensionKey(string $field): array
	{
		return [
			'required_with:' . $field,
			'string',
			'max:32',
		];
	}

	public static function extensionValue(string $field): array
	{
		return [
			'required_with:' . $field,
			'string',
			'max:128',
		];
	}

	public static function errorInformation(): array
	{
		return array_merge(
			[
				'errorInformation' => 'required|array',
				'errorInformation.errorCode' => self::errorCode(),
				'errorInformation.errorDescription' => self::errorDescription(),
			],
			self::extensionList('errorInformation.extensionList')
		);
	}

	public static function errorCode(): string
	{
		return 'regex: /^[1-9]\d{3}$/';
	}

	public static function errorDescription(): string
	{
		return 'string|max:128';
	}

	public static function partyMojaloop(string $field): array
	{
		return array_merge(
			[
				$field . '.partyIdInfo' => 'required|array',
				$field . '.merchantClassificationCode' => self::merchantClassificationCode(),
				$field . '.name' => self::partyName(),
			],
			self::partyIdInfo($field . '.partyIdInfo'),
			self::partyPersonalInfo($field . '.personalInfo'),
		);
	}

	public static function partyIdInfo(string $field): array
	{
		return [
			$field . '.partyIdType' => [
				'required_with:' . $field,
				new EnumValue(PartyIdTypeEnum::class),
			],
			$field . '.partyIdentifier' => 'required_with:' . $field . '|' . self::partyIdentifier(),
			$field . '.partySubIdOrType' => self::partySubIdOrType(),
			$field . '.fspId' => self::fspId(),
		];
	}

	public static function partyIdentifier(): string
	{
		return 'string|max:128';
	}

	public static function partySubIdOrType(): string
	{
		return 'string|max:128';
	}

	public static function fspId(): string
	{
		return 'string|max:32';
	}

	public static function merchantClassificationCode(): string
	{
		return 'regex: /^[\d]{1,4}$/';
	}

	public static function partyName(): string
	{
		return 'string|max:128';
	}

	public static function partyPersonalInfo(string $field): array
	{
		return array_merge(
			[
				$field => 'array',
				$field . '.dateOfBirth' => self::date(),
			],
			self::partyComplexName($field . '.complexName'),
		);
	}

	public static function partyComplexName(string $field): array
	{
		return [
			$field => 'array',
			$field . '.firstName' => self::name(),
			$field . '.middleName' => self::name(),
			$field . '.lastName' => self::name(),
		];
	}

	public static function name(): string
	{
		return 'regex: /^(?!\s*$)[\w .,\'-]{1,128}$/';
	}

	public static function amountType(): EnumValue
	{
		return new EnumValue(AmountTypeEnum::class);
	}

	public static function money(string $field): array
	{
		return [
			$field . '.currency' => [
				'required_with:' . $field,
				new EnumValue(CurrencyMojaEnum::class)
			],
			$field . '.amount' => [
				'required_with:' . $field,
				self::amount(),
			],
		];
	}

	public static function transactionType(string $field): array
	{
		return array_merge(
				[
				$field . '.scenario' => [
					'required',
					new EnumValue(TransactionScenarioEnum::class),
				],
				$field . '.subScenario' => self::transactionSubScenario(),
				$field . '.initiator' => [
					'required',
					new EnumValue(TransactionInitiatorEnum::class),
				],
				$field . '.initiatorType' => [
					'required',
					new EnumValue(TransactionInitiatorTypeEnum::class),
				],
				$field . '.refundInfo' => 'array',
				$field . '.balanceOfPayments' => self::balanceOfPayments(),
			],
			self::refund($field . '.refundInfo')
		);
	}

	public static function transactionSubScenario(): string
	{
		return 'regex: /^[A-Z_]{1,32}$/';
	}

	public static function refund($field): array
	{
		return [
			$field . '.originalTransactionId' => [
				'required_with:' . $field,
				self::correlationId()
			],
			$field . '.refundReason' => self::refundReason(),
		];
	}

	public static function refundReason(): string
	{
		return 'string|max:128';
	}

	public static function balanceOfPayments(): string
	{
		return 'regex: /^[1-9]\d{2}$/';
	}

	public static function geoCodeMoja(string $field): array
	{
		return [
			$field . '.latitude' => [
				'required_with:' . $field,
				'regex: /^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/',
			],
			$field . '.longitude' => [
				'required_with:' . $field,
				'regex: /^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/',
			],
		];
	}

	public static function note(): string
	{
		return 'string|max:128';
	}

	public static function ilpPacket(): string
	{
		return 'string|max:32768|regex: /^[A-Za-z0-9-_]+[=]{0,2}$/';
	}

	public static function ilpCondition(): string
	{
		return 'regex: /^[A-Za-z0-9-_]{43}$/';
	}
}
