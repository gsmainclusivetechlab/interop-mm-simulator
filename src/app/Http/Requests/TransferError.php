<?php

namespace App\Http\Requests;

use App\Concerns\InteractsWithMojaloopValidator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TransferError
 * @package App\Http\Requests
 */
class TransferError extends FormRequest
{
    use InteractsWithMojaloopValidator;
}

