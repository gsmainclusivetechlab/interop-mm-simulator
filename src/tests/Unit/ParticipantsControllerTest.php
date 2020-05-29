<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantsControllerTest extends TestCase
{
    /**
     * Test update.
     *
     * @return void
     */
    public function testUpdate()
    {
        $response = $this->put(
            route('participants.update', [
                'type' => 'MSISDN',
                'id' => '+123123123',
            ])
        );

        $response->assertStatus(200);
    }

    /**
     * Test error.
     *
     * @return void
     */
    public function testError()
    {
        $response = $this->put(
            route('participants.error', [
                'type' => 'MSISDN',
                'id' => '+123123123',
            ])
        );

        $response->assertStatus(200);
    }
}
