<?php


namespace App\Http\Controllers;

use App\Http\Requests\QuoteCreate;
use App\Http\Requests\QuoteUpdate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Env;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class QuotesController extends Controller
{
    /**
     * @param QuoteCreate $request
     * @return string
     */
    public function store(QuoteCreate $request)
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Date' => date('D, d M Y H:i:s T'),
            'FSPIOP-Source' => 'payerfsp',
            'FSPIOP-Destination' => 'payeefsp',
        ];
        $data = $request->getData();

        $client = new Client();
        try {
            $response = $client->request(
                'POST',
                Env::get('HOST_QUOTING_SERVICE') . '/quotes',
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


    /**
     * @param QuoteUpdate $request
     * @param $id
     */
    public function update(QuoteUpdate $request, $id)
    {
        // TODO receive put /quotes from mojaloop
    }
}
