<?php


namespace App\Http\Controllers;

use App\Http\Requests\TransferCreate;
use App\Http\Requests\TransferError;
use App\Http\Requests\TransferUpdate;
use App\Requests\Callback;
use \GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;

/**
 * Class TransfersController
 * @package App\Http\Controllers
 */
class TransfersController extends Controller
{
    /**
     * Handle transfer request
     *
     * @param TransferCreate $request
     * @return array|string
     * @throws \Exception
     */
    public function store(TransferCreate $request)
    {
        app()->terminating(function() use ($request) {
            $data = $request->mapInTo();

            Callback::send($request, $data);

            $client = new Client();
            $response = $client->request(
                'PUT',
                Env::get('HOST_ML_API_ADAPTER') . '/transfers/' . $request->transferId,
                [
                    'headers' => [
                        'traceparent'        => $request->header('traceparent'),
                        'Content-Type'       => 'application/vnd.interoperability.transfers+json;version=1.0',
                        'Date'               => (new Carbon())->toRfc7231String(),
                        'FSPIOP-Source'      => $request->header('FSPIOP-Destination'),
                        'FSPIOP-Destination' => $request->header('FSPIOP-Source'),
                    ],
                    'json' => $data,
                ]
            );
            \Illuminate\Support\Facades\Log::info(
                'PUT /transfers ' . $response->getStatusCode() . PHP_EOL
                . \GuzzleHttp\json_encode($data) . PHP_EOL
            );
        });

        return new Response(202);
    }

    /**
     * @param TransferUpdate $request
     * @param $id
     */
    public function update(TransferUpdate $request, $id)
    {
    }

    /**
     * @param TransferError $request
     * @param $id
     */
    public function error(TransferError $request, $id)
    {
    }
}
