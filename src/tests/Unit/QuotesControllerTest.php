<?php

namespace Tests\Unit;

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
        $this->put(
            route('quotes.update', ['id' => '+123123123']),
            [
                'errorInformation' => [
                    'errorCode' => '1234',
                    'errorDescription' => 'errorDescription',
                ],
            ])->assertStatus(302);

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
