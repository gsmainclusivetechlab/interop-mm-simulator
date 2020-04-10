<?php

namespace App\Http\Requests;

use App\Http\ValidationSets;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequestError extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ValidationSets::errorInformation();
    }
}
