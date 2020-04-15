<?php

namespace App\Http\Requests;

use App\Concerns\InteractsWithMojaloopValidator;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequestUpdate extends FormRequest
{
    use InteractsWithMojaloopValidator;
}
