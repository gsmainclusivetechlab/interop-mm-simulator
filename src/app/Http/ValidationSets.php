<?php

namespace App\Http;

use App\Enums\CurrencyEnum;
use App\Enums\DeliveryMethodEnum;
use App\Enums\GenderEnum;
use App\Enums\IdTypeEnum;
use App\Enums\NationalityEnum;
use App\Enums\TransactionRequestStateEnum;
use App\Enums\TypeEnum;
use BenSampo\Enum\Rules\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Static_;

/**
 * Frequentsy used Validation Sets
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
	public static function amount(): array
	{
		return [
			'required',
			'regex:/^([0]|([1-9][0-9]{0,17}))([.][0-9]{0,3}[1-9])?$/',
		];
	}

	/**
	 * Currency
	 *
	 * @return array
	 */
	public static function currency(): array
	{
		return [
			'required',
			new EnumValue(CurrencyEnum::class),
		];
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
			static::partyMMO($field)
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
			$field . '.*.key' => static::requiredString(),
			$field . '.*.value' => static::requiredString(),
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
	public static function geoCode(): array
	{
		return [
			'nullable',
			'regex: /^(-?(90|(\\d|[1-8]\\d)(\\.\\d{1,6}){0,1}))\\,{1}(-?(180|(\\d|\\d\\d|1[0-7]\\d)(\\.\\d{1,6}){0,1}))$/'
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
				$field . '.' . 'nationality' => static::nationality(),
				$field . '.' . 'dateOfBirth' => static::date(),
				$field . '.' . 'occupation' => static::standardString(),
				$field . '.' . 'employerName' => static::standardString(),
				$field . '.' . 'contactPhone' => static::standardString(),
				$field . '.' . 'gender' => new EnumValue(GenderEnum::class),
				$field . '.' . 'emailAddress' => static::standardString(),
				$field . '.' . 'birthCountry' => static::nationality(),
			],
			static::idDocumentArray($field . '.idDocument'),
			static::postalAddress($field . '.postalAddress'),
			static::subjectName($field . '.subjectName')
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
			static::idDocument($field)
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
			$field . '.*.idNumber' => static::standardString(),
			$field . '.*.issueDate' => static::date(),
			$field . '.*.expiryDate' => static::date(),
			$field . '.*.issuer' => static::standardString(),
			$field . '.*.issuerPlace' => static::standardString(),
			$field . '.*.issuerCountry' => static::nationality(),
			$field . '.*.otherIddescription' => static::standardString(),
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
			$field . '.addressLine1' => static::standardString(),
			$field . '.addressLine2' => static::standardString(),
			$field . '.addressLine3' => static::standardString(),
			$field . '.city' => static::standardString(),
			$field . '.stateProvince' => static::standardString(),
			$field . '.postalCode' => static::standardString(),
			$field . '.country' => [
				'required_with:' . $field,
				static::nationality(),
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
			$field . '.title' => static::standardString(),
			$field . '.fullName' => static::standardString(),
			$field . '.firstName' => static::standardString(),
			$field . '.middleName' => static::standardString(),
			$field . '.lastName' => static::standardString(),
			$field . '.nativeName' => static::standardString(),
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
			static::fees($field)
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
			$field . '.*.feeType' => static::requiredString(),
			$field . '.*.feeAmount' => static::amount(),
			$field . '.*.feeCurrency' => static::currency(),
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
			static::metadata($field)
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
			$field . '.*.key' => static::requiredString(),
			$field . '.*.value' => static::requiredString(),
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
				static::nationality(),
			],
			$field . '.quotationReference' => static::standardString(),
			$field . '.quoteId' => static::standardString(),
			$field . '.receivingCountry' => static::nationality(),
			$field . '.remittancePurpose' => static::standardString(),
			$field . '.relationshipSender' => static::standardString(),
			$field . '.deliveryMethod' => static::deliveryMethod(),
		];
	}

	/**
	 * @return EnumValue
	 */
	public static function deliveryMethod(): EnumValue
	{
		return new EnumValue(DeliveryMethodEnum::class);
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
		], static::extension($field . '.extension'));
	}

	public static function extension(string $field): array
	{
		return [
			$field . '.key' => static::extensionKey($field . '.key'),
			$field . '.value' => static::extensionValue($field . '.value'),
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
		return [
			'errorInformation' => 'required|array',
			'errorInformation.errorCode' => static::errorCode(),
			'errorInformation.errorDescription' => static::errorDescription(),
			'errorInformation.extensionList' => static::extensionList('errorInformation.extensionList'),
		];
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
		return array_merge([
			$field => 'array',
			$field . '.partyIdInfo' => 'required|array',
			$field . '.merchantClassificationCode' => ,
			$field . '.name' => ,
			$field . '.personalInfo' => ,
		], static::partyIdInfo($field . '.partyIdInfo'));
	}

	public static function partyIdInfo(string $field): array
	{
		return [
			$field . '.partyIdType' => [
				'required',
				self::partyIdType(),
			],
			$field . '.partyIdentifier' => 'required',
			$field . '.partySubIdOrType' => '',
			$field . '.fspId' => '',
		];
	}

	public static function partyIdType(): string
	{
		return '';
	}
}