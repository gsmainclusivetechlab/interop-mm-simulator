<?php


namespace App\Http\Controllers;

use App\Http\Requests\TransactionCreate;
use \GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\URL;

/**
 * Class TransactionsController
 *
 * @package App\Http\Controllers
 */
class TransactionsController extends Controller
{
    /**
     * Request from service provider
     * Create A Transaction
     *
     * @param TransactionCreate $request
     * @return mixed
     */
    public function store(TransactionCreate $request)
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Date' => date('D, d M Y H:i:s T'),
            'FSPIOP-Source' => 'payerfsp',
            'FSPIOP-Destination' => 'payeefsp',
        ];
        $data = $request->dataMapping();

        $client = new Client();
        try {
            $response = $client->request(
                'POST',
                Env::get('HOST_TRANSACTION_REQUESTS_SERVICE') . '/transactionRequests',
                [
                    'headers' => $headers,
                    'json' => \GuzzleHttp\json_encode($data),
                    'debug' => true,
                ]
            );
        } catch (BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }

        return $response->getBody()->getContents();
    }
}
