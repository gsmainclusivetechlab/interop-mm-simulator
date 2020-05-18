<?php

namespace App\Http\Requests;

use App\Http\ValidationSets;
use App\Traits\ParseTraceId;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TransferCreate
 * @package App\Http\Requests
 *
 * @property string $transferId
 * @property array $amount
 * @property string $transferState
 */
class TransferCreate extends FormRequest
{
    use ParseTraceId;

    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
			[
				'transferId' => [
					'required',
					ValidationSets::correlationId(),
				],
				'payeeFsp' => 'required|' . ValidationSets::fspId(),
				'payerFsp' => 'required|' . ValidationSets::fspId(),
				'amount' => 'required|array',
				'ilpPacket' => 'required|' . ValidationSets::ilpPacket(),
				'condition' => [
					'required',
					ValidationSets::ilpCondition(),
				],
				'expiration' => [
					'required',
					ValidationSets::dateTime(),
				],
				'extensionList' => ValidationSets::extensionList('extensionList'),
			],
			ValidationSets::money('amount')
		);
    }

    /**
     * @param $completedTimestamp
     * @return array
     */
    public function mapInTo($completedTimestamp): array
    {
        return [
            'fulfilment' => 'XoSz1cL0tljJSCp_VtIYmPNw-zFUgGfbUqf69AagUzY',
            'completedTimestamp' => $completedTimestamp,
            'transferState' => 'COMMITTED',
        ];
    }
}

