<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Tests\TestCase;

class QuotesControllerTest extends TestCase
{
    /**
     * Test update.
     *
     * @return void
     */
    public function testUpdate()
    {
        // Create a mock HTTP client for our test to use instead of the ITP platform
        $mock = new MockHandler([
            new Response(200),
        ]);
        $requests = [];
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($requests));
        $client = new Client(['handler' => $handlerStack]);
        $this->instance(Client::class, $client);

        $response = $this->withHeaders([
            "traceparent" => "parent-trace-id",
            "FSPIOP-Source" => "source",
            "FSPIOP-Destination" => "destination"
        ])->putJson(
            route('quotes.update', ['id' => '+123123123']),
            [
                "transferAmount" => [
                    "amount" => 1001,
                    "currency" => "USD"
                ],
                'expiration' => (new Carbon())
                    ->addSeconds(600000)
                    ->toIso8601ZuluString('millisecond'),
                'ilpPacket' =>
                    'AQAAAAAAAADIEHByaXZhdGUucGF5ZWVmc3CCAiB7InRyYW5zYWN0aW9uSWQiOiIyZGY3NzRlMi1mMWRiLTRmZjctYTQ5NS0yZGRkMzdhZjdjMmMiLCJxdW90ZUlkIjoiMDNhNjA1NTAtNmYyZi00NTU2LThlMDQtMDcwM2UzOWI4N2ZmIiwicGF5ZWUiOnsicGFydHlJZEluZm8iOnsicGFydHlJZFR5cGUiOiJNU0lTRE4iLCJwYXJ0eUlkZW50aWZpZXIiOiIyNzcxMzgwMzkxMyIsImZzcElkIjoicGF5ZWVmc3AifSwicGVyc29uYWxJbmZvIjp7ImNvbXBsZXhOYW1lIjp7fX19LCJwYXllciI6eyJwYXJ0eUlkSW5mbyI6eyJwYXJ0eUlkVHlwZSI6Ik1TSVNETiIsInBhcnR5SWRlbnRpZmllciI6IjI3NzEzODAzOTExIiwiZnNwSWQiOiJwYXllcmZzcCJ9LCJwZXJzb25hbEluZm8iOnsiY29tcGxleE5hbWUiOnt9fX0sImFtb3VudCI6eyJjdXJyZW5jeSI6IlVTRCIsImFtb3VudCI6IjIwMCJ9LCJ0cmFuc2FjdGlvblR5cGUiOnsic2NlbmFyaW8iOiJERVBPU0lUIiwic3ViU2NlbmFyaW8iOiJERVBPU0lUIiwiaW5pdGlhdG9yIjoiUEFZRVIiLCJpbml0aWF0b3JUeXBlIjoiQ09OU1VNRVIiLCJyZWZ1bmRJbmZvIjp7fX19',
                'condition' => 'HOr22-H3AfTDHrSkPjJtVPRdKouuMkDXTR4ejlQa8Ks',
            ]);

        // inspect the response we sent to the caller
        $response->assertStatus(200);

        // inspect the request we constructed to ensure it's correct
        $payload = json_decode($requests[0]['request']->getBody());
        $this->assertCount(1, $requests);
        $this->assertEquals("POST", $requests[0]['request']->getMethod());
        $this->assertEquals(
            Env::get('HOST_ML_API_ADAPTER') . 'transfers',
            (string)$requests[0]['request']->getUri()
        );
        $this->assertEquals("destination", $payload->payerFsp);
        $this->assertEquals("source", $payload->payeeFsp);
        $this->assertTrue(is_string($payload->transferId));
    }

    /**
     * Test error.
     *
     * @return void
     */
    public function testError()
    {
        $response = $this->put(
            route('quotes.error', ['id' => '+123123123']),
            [
                'errorInformation' => [
                    'errorCode' => '1234',
                    'errorDescription' => 'errorDescription',
                ],
            ]);

        $response->assertStatus(200);
    }
}
