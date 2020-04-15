<?php

namespace App\Http\Requests;

use App\Concerns\InteractsWithMojaloopValidator;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequestError extends FormRequest
{
    use InteractsWithMojaloopValidator;
}
