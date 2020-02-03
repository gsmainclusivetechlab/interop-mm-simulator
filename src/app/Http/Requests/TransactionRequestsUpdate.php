<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequestsUpdate extends FormRequest
{
    public function rules()
    {
        return [
            'fulfilment' => 'string',
            'completedTimestamp' => 'string',
            'transferState' => 'required|string',
            'extensionList' => 'required|array',
                'extensionList.*.key' => 'required|string',
                'extensionList.*.value' => 'required|string',
        ];
    }
}

