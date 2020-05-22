<?php

namespace App\Requests;

use Carbon\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Str;

/**
 * Class QuoteStore
 *
 * @package App\Requests
 */
class QuoteStore extends BaseRequest
{
    /**
     * QuoteStore constructor.
     *
     * @param array $data
     * @param array $headers
     * @throws \Exception
     */
    public function __construct(array $data, array $headers)
    {
        parent::__construct($data, $headers, Env::get('HOST_QUOTING_SERVICE') . 'quotes');

        $this->method = 'POST';

        $this->headers['Date'] = (new Carbon())->toRfc7231String();
        $this->headers['Accept'] = 'application/vnd.interoperability.quotes+json;version=1.0';
        $this->headers['Content-Type'] = 'application/vnd.interoperability.quotes+json;version=1.0';
        $this->headers['FSPIOP-Source'] = Env::get('FSPIOP_SOURCE');
        $this->headers['authorization'] = 'Bearer {{TESTFSP1_BEARER_TOKEN}}';
        $this->headers['FSPIOP-Signature'] = '{"signature":"iU4GBXSfY8twZMj1zXX1CTe3LDO8Zvgui53icrriBxCUF_wltQmnjgWLWI4ZUEueVeOeTbDPBZazpBWYvBYpl5WJSUoXi14nVlangcsmu2vYkQUPmHtjOW-yb2ng6_aPfwd7oHLWrWzcsjTF-S4dW7GZRPHEbY_qCOhEwmmMOnE1FWF1OLvP0dM0r4y7FlnrZNhmuVIFhk_pMbEC44rtQmMFv4pm4EVGqmIm3eyXz0GkX8q_O1kGBoyIeV_P6RRcZ0nL6YUVMhPFSLJo6CIhL2zPm54Qdl2nVzDFWn_shVyV0Cl5vpcMJxJ--O_Zcbmpv6lxqDdygTC782Ob3CNMvg\\",\\"protectedHeader\\":\\"eyJhbGciOiJSUzI1NiIsIkZTUElPUC1VUkkiOiIvdHJhbnNmZXJzIiwiRlNQSU9QLUhUVFAtTWV0aG9kIjoiUE9TVCIsIkZTUElPUC1Tb3VyY2UiOiJPTUwiLCJGU1BJT1AtRGVzdGluYXRpb24iOiJNVE5Nb2JpbGVNb25leSIsIkRhdGUiOiIifQ"}';
    }

    /**
     * @param $partyIdentifier
     * @return array
     */
    public static function mapInTo($partyIdentifier)
    {
        return array_merge(
            [
                'quoteId' => Str::uuid(),
                'transactionId' => Str::uuid(),
                'payer' => [
                    'partyIdInfo' => [
                        'partyIdType' => 'MSISDN',
                        'partyIdentifier' => '+33555123456',
                    ],
                    'personalInfo' => [
                        'complexName' => [
                            'firstName' => 'John',
                            'middleName' => 'Jane',
                            'lastName' => 'Doe',
                        ],
                        'dateOfBirth' => '2000-01-01'
                    ]
                ],
                'transactionType' => [
                    'scenario' => 'TRANSFER',
                    'initiator' => 'PAYER',
                    'initiatorType' => 'CONSUMER'
                ],
            ],
            static::participantsData()[$partyIdentifier]
        );
    }

    public static function participantsData()
    {
        return [
            Env::get('PARTICIPANTS_ID_P2P_500') => [
                'amount' => [
                    'amount' => '799',
                    'currency' => 'USD',
                ],
                'payerFspFee' =>[
                    'currency' => 'USD',
                    'amount' => '1'
                ],
                'amountType' => 'SEND',
                'payee' => [
                    'partyIdInfo' => [
                        'partyIdType' => 'MSISDN',
                        'partyIdentifier' => Env::get('PARTICIPANTS_ID_P2P_800'),
                    ]
                ],
            ],
            Env::get('PARTICIPANTS_ID_P2P_600') => [
                'amount' => [
                    'amount' => '799',
                    'currency' => 'USD',
                ],
                'payerFspFee' =>[
                    'currency' => 'USD',
                    'amount' => '1'
                ],
                'amountType' => 'SEND',
                'payee' => [
                    'partyIdInfo' => [
                        'partyIdType' => 'MSISDN',
                        'partyIdentifier' => Env::get('PARTICIPANTS_ID_P2P_800'),
                    ]
                ],
            ],
            Env::get('PARTICIPANTS_ID_P2P_700') => [
                'amount' => [
                    'amount' => '799',
                    'currency' => 'USD',
                ],
                'payerFspFee' =>[
                    'currency' => 'USD',
                    'amount' => '1'
                ],
                'amountType' => 'SEND',
                'payee' => [
                    'partyIdInfo' => [
                        'partyIdType' => 'MSISDN',
                        'partyIdentifier' => Env::get('PARTICIPANTS_ID_P2P_700'),
                    ]
                ],
            ],
            Env::get('PARTICIPANTS_ID_P2P_800') => [
                'amount' => [
                    'amount' => '799',
                    'currency' => 'USD',
                ],
                'payerFspFee' =>[
                    'currency' => 'USD',
                    'amount' => '1'
                ],
                'amountType' => 'SEND',
                'payee' => [
                    'partyIdInfo' => [
                        'partyIdType' => 'MSISDN',
                        'partyIdentifier' => Env::get('PARTICIPANTS_ID_P2P_800'),
                    ]
                ],
            ],
        ];
    }
}
