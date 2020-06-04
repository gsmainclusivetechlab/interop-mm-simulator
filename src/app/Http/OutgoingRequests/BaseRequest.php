<?php

namespace App\Requests;

use App\Contracts\RequestContract;
use App\Traits\ParseTraceId;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

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
     * @var Client
     */
    protected $client;

    /**
     * BaseRequest constructor.
     *
     * @param array $data
     * @param array $headers
     * @param string $url
     * @param Client|null $client
     */
    public function __construct(array $data, array $headers, string $url, Client $client = null)
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->url = $url;
        $this->client = $client == null ? new Client() : $client;
    }

    /**
     * @inheritDoc
     */
    public function send(): ResponseInterface
    {
        try {
            $response = $this->client->request($this->method, $this->url, [
                'headers' => $this->headers,
                'json' => $this->data,
            ]);

        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        Log::info(
            "{$this->method} " .
            $this->url .
            ' ' .
            $response->getStatusCode() .
            PHP_EOL .
            json_encode($this->data) .
            PHP_EOL
        );

        return $response;
    }
}
