<?php

namespace App\Http\Requests;

use App\Http\RuleSets;
use App\Traits\ParseTraceId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

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
					RuleSets::correlationId(),
				],
				'payeeFsp' => 'required|' . RuleSets::fspId(),
				'payerFsp' => 'required|' . RuleSets::fspId(),
				'amount' => 'required|array',
				'ilpPacket' => 'required|' . RuleSets::ilpPacket(),
				'condition' => [
					'required',
					RuleSets::ilpCondition(),
				],
				'expiration' => [
					'required',
					RuleSets::dateTime(),
				],
				'extensionList' => RuleSets::extensionList('extensionList'),
			],
			RuleSets::money('amount')
		);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function mapInTo(): array
    {
        return [
            'fulfilment' => 'XoSz1cL0tljJSCp_VtIYmPNw-zFUgGfbUqf69AagUzY',
            'completedTimestamp' => (new Carbon())->toIso8601ZuluString('millisecond'),
            'transferState' => 'COMMITTED',
        ];
    }
}

