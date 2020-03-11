<?php


namespace App\Requests;


use App\Models\Transaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class Callback
 * @package App\Requests
 */
class Callback
{
    /**
     * Send
     *
     * @param Request $request
     */
    public static function send(Request $request, array $data)
    {
        $traceparentParts = explode('-', $request->header('traceparent'), 3);
        $traceparent = $traceparentParts[0] . '-' . $traceparentParts[1];
        $transaction = Transaction::where('traceparent', '=', $traceparent)->firstOrFail();

        $url = $transaction->callback_url;

        if (!$url) {
            return;
        }

        $client = new Client();

        $response = $client->request(
            'PUT',
            $url,
            $data
        );

        $transaction->delete();

        Log::info(
            'PUT ' . $url . ' ' . $response->getStatusCode() . PHP_EOL
            . \GuzzleHttp\json_encode($data) . PHP_EOL
        );
    }
}
