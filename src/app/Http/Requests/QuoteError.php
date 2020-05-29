<?php

namespace App\Http\Requests;

use App\Http\ValidationSets;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class QuoteError
 * @package App\Http\Requests
 */
class QuoteError extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return ValidationSets::errorInformation();
    }
}
