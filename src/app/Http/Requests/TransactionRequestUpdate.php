<?php

namespace App\Http\Requests;

use App\Http\RuleSets;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequestUpdate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transactionId' => RuleSets::correlationId(),
			'transactionRequestState' => RuleSets::transactiornRequestState(),
			'extensionList' => RuleSets::extensionList('extensionList'),
        ];
    }
}
