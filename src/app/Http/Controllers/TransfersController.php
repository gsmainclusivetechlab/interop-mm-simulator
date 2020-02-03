<?php


namespace App\Http\Controllers;

use App\Http\Requests\TransferCreate;
use App\Http\Requests\TransferUpdate;
use \GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class TransfersController extends Controller
{
    /**
     * @param TransferCreate $request
     * @return string
     */
    public function store(TransferCreate $request)
    {
        $headers = [
            'Accept' => 'application/vnd.interoperability.quotes+json;version=1',
            'Content-Type' => 'application/vnd.interoperability.quotes+json;version=1.0',
            'Cache-Control' => 'no-cache', //
            'Date' => date('D, d M Y H:i:s ').'GMT',
            'FSPIOP-Source' => 'payerfsp',
            'FSPIOP-Destination' => 'payeefsp',
        ];

        $data = [
            'transferId' => Str::uuid(),
            'payeeFsp' => 'payeefsp',
            'payerFsp' => 'payerfsp',
            'amount' => [
                'amount' => '2',
                'currency' => 'USD'
            ],
            'ilpPacket' => 'AQAAAAAAAABkEGcuZXdwMjEuaWQuODAwMjCCAhd7InRyYW5zYWN0aW9uSWQiOiJmODU0NzdkYi0xMzVkLTRlMDgtYThiNy0xMmIyMmQ4MmMwZDYiLCJxdW90ZUlkIjoiOWU2NGYzMjEtYzMyNC00ZDI0LTg5MmYtYzQ3ZWY0ZThkZTkxIiwicGF5ZWUiOnsicGFydHlJZEluZm8iOnsicGFydHlJZFR5cGUiOiJNU0lTRE4iLCJwYXJ0eUlkZW50aWZpZXIiOiIyNTYxMjM0NTYiLCJmc3BJZCI6IjIxIn19LCJwYXllciI6eyJwYXJ0eUlkSW5mbyI6eyJwYXJ0eUlkVHlwZSI6Ik1TSVNETiIsInBhcnR5SWRlbnRpZmllciI6IjI1NjIwMTAwMDAxIiwiZnNwSWQiOiIyMCJ9LCJwZXJzb25hbEluZm8iOnsiY29tcGxleE5hbWUiOnsiZmlyc3ROYW1lIjoiTWF0cyIsImxhc3ROYW1lIjoiSGFnbWFuIn0sImRhdGVPZkJpcnRoIjoiMTk4My0xMC0yNSJ9fSwiYW1vdW50Ijp7ImFtb3VudCI6IjEwMCIsImN1cnJlbmN5IjoiVVNEIn0sInRyYW5zYWN0aW9uVHlwZSI6eyJzY2VuYXJpbyI6IlRSQU5TRkVSIiwiaW5pdGlhdG9yIjoiUEFZRVIiLCJpbml0aWF0b3JUeXBlIjoiQ09OU1VNRVIifSwibm90ZSI6ImhlaiJ9',
            'condition' => 'otTwY9oJKLBrWmLI4h0FEw4ksdZtoAkX3qOVAygUlTI',
            'expiration' => '2020-10-05T14:48:00.000Z',
        ];

        $client = new Client();
        try {
            $response = $client->request(
                'POST',
                Env::get('HOST_ML_API_ADAPTER') . '/transfers',
                [
                    'headers' => $headers,
                    'json' => \GuzzleHttp\json_encode($data),
                    'debug' => true,
                ]
            );
        } catch (GuzzleException $e) {
            return $e->getResponse()->getBody()->getContents();
        }

        return $response->getBody()->getContents();
    }


    /**
     * @param TransferUpdate $request
     * @param $id
     */
    public function update(TransferUpdate $request, $id)
    {
        Log::info(
            'PUT /transfers:' . PHP_EOL
            . $request->getContent() . PHP_EOL
            . 'id = ' . $id . PHP_EOL
        );
    }
}
