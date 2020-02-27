<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

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
            'fulfilment' => 'XoSz1cL0tljJSCp_VtIYmPNw-zFUgGfbUqf69AagUzY',
            'completedTimestamp' => (new Carbon())->toIso8601ZuluString('millisecond'),
            'transferState' => 'COMMITTED',
        ];
    }
}

