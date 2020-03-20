<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use App\Traits\ParseTraceId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
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
        return [
            'transferId' => 'string|required',
        ];
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

