<?php

namespace App\Requests;

use App\Contracts\RequestContract;
use App\Traits\ParseTraceId;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;

/**
 * Class BaseRequest
 *
 * @package App\Requests
 *
 * @property string $method
 * @property array $data
 * @property array $headers
 * @property string $url
 */
class BaseRequest implements RequestContract
{
    use ParseTraceId;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $url;

    /**
     * BaseRequest constructor.
     *
     * @param array $data
     * @param array $headers
     * @param string $url
     */
    public function __construct(array $data, array $headers, string $url)
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function send(): bool
    {
        $client = new Client();

        try {
            $response = $client->request(
                $this->method,
                $this->url,
                [
                    'headers' => $this->headers,
                    'json' => $this->data
                ]
            );

            $responseLog = $response->getBody()->getContents();
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $responseLog = $e->getMessage();
        }

        Log::info(
            "{$this->method} " . $this->url . ' ' . $response->getStatusCode() . PHP_EOL
            . json_encode($this->data) . PHP_EOL
            . $responseLog
        );

        return true;
    }
}
