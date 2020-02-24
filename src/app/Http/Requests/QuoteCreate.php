<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class QuoteCreate
 * @package App\Http\Requests
 *
 * @property string $quoteId
 * @property string $transactionId
 * @property string $transactionRequestId
 * @property array $amount
 */
class QuoteCreate extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'quoteId'              => 'string|required',
            'transactionId'        => 'string|required',
            'transactionRequestId' => 'string',
            'amount'               => 'array|required',
                'amount.amount'   => 'string|required',
                'amount.currency' => 'string|required',
        ];
    }

    /**
     * payer -> debit
     * payee -> credit
     *
     * @return array
     * @throws \Exception
     */
    public function mapInTo()
    {
        return [
            'transferAmount' => [
                'amount'   => $this->amount['amount'],
                'currency' => $this->amount['currency'],
            ],
            'expiration'     => (new Carbon())->addSeconds(600000)->toIso8601ZuluString('millisecond'),
            'ilpPacket'      => Str::random(764),
            'condition'      => Str::random(43),
        ];
    }
}

