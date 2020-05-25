<?php

namespace App\Http\Controllers;

use App\Http\Headers;
use App\Models\Transaction;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Class AuthorizationsController
 * @package App\Http\Controllers
 */
class AuthorizationsController extends Controller
{
    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        app()->terminating(function() use ($request, $id) {
            if (!$transaction = Transaction::whereTransactionId($id)->first()) {
                $transaction = Transaction::getCurrent();
            }

            $response = (new \App\Requests\AccountStore(
                [
                    'amount' => $request->query('amount'),
                    'currency' => $request->query('currency'),
                    'requestDate' => (new Carbon())->toIso8601ZuluString('millisecond')
                ],
                ['traceparent' => $request->header('traceparent')],
                strtolower($transaction->debitParty[0]['key']),
                $transaction->debitParty[0]['value']
            ))->send();

            if ($response->getStatusCode() === 201) {

                (new \App\Requests\AuthorizationUpdate([
                    'authenticationInfo' => [
                        'authentication' => $request->query('authenticationType') ?? '', // OTP , QRCODE
                        'authenticationValue' => \GuzzleHttp\json_decode($response->getBody())->authorisationCode ?? '' // ^\d{3,10}$|^\S{1,64}$
                    ],
                    'responseType' => 'ENTERED', // ENTERED , REJECTED , RESEND
                ], [
                    'traceparent'        => $request->header('traceparent'),
                    'FSPIOP-Source'      => $request->header('FSPIOP-Destination'),
                    'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
                ], $id))->send();
            }
        });

        return new Response(
            202,
            [
                'Content-Type' => 'application/json',
                'X-Date' => Headers::getXDate()
            ]
        );
    }
}
