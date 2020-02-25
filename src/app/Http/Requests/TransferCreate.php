<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class TransferCreate
 * @package App\Http\Requests
 *
 * @property string $transferId
 */
class TransferCreate extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'transferId' => 'string|required',
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function mapInTo()
    {
        return [
            'fulfilment' => Str::random(43),
            'completedTimestamp' => (new Carbon())->toIso8601ZuluString('millisecond'),
            'transferState' => 'COMMITTED',
        ];
    }
}

