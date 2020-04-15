<?php

namespace App\Http\Requests;

use App\Concerns\InteractsWithMojaloopValidator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class QuoteError
 * @package App\Http\Requests
 */
class QuoteError extends FormRequest
{
    use InteractsWithMojaloopValidator;
}

