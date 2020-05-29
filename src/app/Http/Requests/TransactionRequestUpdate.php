<?php

namespace App\Http\Requests;

use App\Enums\TransactionRequestStateEnum;
use App\Http\ValidationSets;
use BenSampo\Enum\Rules\EnumValue;
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
            'transactionId' => ValidationSets::correlationId(),
            'transactionRequestState' => new EnumValue(
                TransactionRequestStateEnum::class
            ),
            'extensionList' => ValidationSets::extensionList('extensionList'),
        ];
    }
}
