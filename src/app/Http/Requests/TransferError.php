<?php

namespace App\Http\Requests;

use App\Http\ValidationSets;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TransferError
 * @package App\Http\Requests
 */
class TransferError extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return ValidationSets::errorInformation();
    }
}
