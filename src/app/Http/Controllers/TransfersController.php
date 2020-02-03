<?php


namespace App\Http\Controllers;

use App\Http\Requests\TransferCreate;
use App\Http\Requests\TransferUpdate;
use \GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
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
            'Date' => date('D, d M Y H:i:s ') . 'GMT',
            'FSPIOP-Source' => 'payerfsp',
            'FSPIOP-Destination' => 'payeefsp',
            'FSPIOP-Signature' => '{\"signature\":\"iU4GBXSfY8twZMj1zXX1CTe3LDO8Zvgui53icrriBxCUF_wltQmnjgWLWI4ZUEueVeOeTbDPBZazpBWYvBYpl5WJSUoXi14nVlangcsmu2vYkQUPmHtjOW-yb2ng6_aPfwd7oHLWrWzcsjTF-S4dW7GZRPHEbY_qCOhEwmmMOnE1FWF1OLvP0dM0r4y7FlnrZNhmuVIFhk_pMbEC44rtQmMFv4pm4EVGqmIm3eyXz0GkX8q_O1kGBoyIeV_P6RRcZ0nL6YUVMhPFSLJo6CIhL2zPm54Qdl2nVzDFWn_shVyV0Cl5vpcMJxJ--O_Zcbmpv6lxqDdygTC782Ob3CNMvg\\\",\\\"protectedHeader\\\":\\\"eyJhbGciOiJSUzI1NiIsIkZTUElPUC1VUkkiOiIvdHJhbnNmZXJzIiwiRlNQSU9QLUhUVFAtTWV0aG9kIjoiUE9TVCIsIkZTUElPUC1Tb3VyY2UiOiJPTUwiLCJGU1BJT1AtRGVzdGluYXRpb24iOiJNVE5Nb2JpbGVNb25leSIsIkRhdGUiOiIifQ\"}',
            'FSPIOP-URI' => '/transfers',
            'FSPIOP-HTTP-Method' => 'POST',
        ];

        $data = $request->getData();

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
        } catch (BadResponseException $e) {
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
