<?php

namespace App\Requests;

use App\Models\Transaction;
use App\Traits\ParseTraceId;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class Callback
 * @package App\Requests
 */
class Callback
{
    use ParseTraceId;

    /**
     * Send
     *
     * @param string $url
     * @param array $headers
     * @param array $data
     *
     * @return bool
     */
    public static function send(string $url, array $headers, array $data): bool
    {
        if (!$url) {
            return false;
        }

        $client = new Client();

        try {
            $response = $client->request(
                'PUT',
                $url,
                [
                    'headers' => $headers,
                    'json' => $data
                ]
            );

            $responseLog = $response->getBody()->getContents();
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $responseLog = $e->getMessage();
        }

        Log::info(
            'PUT ' . $url . ' ' . $response->getStatusCode() . PHP_EOL
            . json_encode($data) . PHP_EOL
            . $responseLog
        );

        return true;
    }
}
