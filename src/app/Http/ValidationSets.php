<?php

namespace App\Http;

use App\Enums\CurrencyEnum;
use App\Enums\DeliveryMethodEnum;
use App\Enums\GenderEnum;
use App\Enums\IdTypeEnum;
use App\Enums\NationalityEnum;
use App\Enums\TypeEnum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\Arr;

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
			static::party($field)
		);

		return $rules;
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	public static function party(string $field): array
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
}