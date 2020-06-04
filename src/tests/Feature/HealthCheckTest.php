<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Check health check status is 200
     *
     * @return void
     */
    public function testHealthCheck()
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
    }
}
