<?php

namespace App\Http\Requests;

use App\Http\ValidationSets;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class QuoteUpdate
 * @package App\Http\Requests
 *
 * @property array $transferAmount
 * @property string $ilpPacket
 * @property string $condition
 * @property string $expiration
 */
class QuoteUpdate extends FormRequest
{
    public function rules()
    {
        return [
            'transferAmount' => [
                'required',
                'array',
                ValidationSets::money('transferAmount')
            ],
            'ilpPacket' => 'required|' . ValidationSets::ilpPacket(),
            'condition' => [
                'required',
                ValidationSets::ilpCondition(),
            ],
            'expiration' => [
                'required',
                ValidationSets::dateTime(),
            ],
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function mapInTo(): array
    {
        return [
            'transferId' => Str::uuid(),
            'payerFsp'   => $this->header('FSPIOP-Destination'),
            'payeeFsp'   => $this->header('FSPIOP-Source'),
            'amount'     => [
                'amount'   => $this->transferAmount['amount'],
                'currency' => $this->transferAmount['currency'],
            ],
            'expiration' => (new Carbon())->addSeconds(600000)->toIso8601ZuluString('millisecond'),
            'ilpPacket'  => $this->ilpPacket,
            'condition'  => $this->condition,
        ];
    }
}

