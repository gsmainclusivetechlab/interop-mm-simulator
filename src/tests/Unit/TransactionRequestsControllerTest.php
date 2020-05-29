<?php

namespace Tests\Unit;

use App\Enums\TransactionRequestStateEnum;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransactionRequestsControllerTest extends TestCase
{
    /**
     * Test update with rejected status.
     *
     * @return void
     */
    public function testUpdateRejected()
    {
        $this->withoutEvents();
        $this->withoutExceptionHandling();
        $model = factory(Transaction::class)->create();
        $data = [
            'transactionId' => Str::uuid()->toString(),
            'transactionRequestState' => TransactionRequestStateEnum::REJECTED,
        ];
        $response = $this->put(
            route('transaction_requests.update', ['id' => Str::uuid()]),
            $data,
            [
                'traceparent' => '00-' . $model->trace_id
            ]
        );

        $response->assertStatus(200);
    }

    /**
     * Test update with not received status.
     *
     * @return void
     */
    public function testUpdateReceived()
    {
        $this->withoutEvents();
        $model = factory(Transaction::class)->create();
        $data = [
            'transactionId' => Str::uuid()->toString(),
            'transactionRequestState' => TransactionRequestStateEnum::RECEIVED,
        ];
        $response = $this->put(
            route('transaction_requests.update', ['id' => Str::uuid()]),
            $data,
            [
                'traceparent' => '00-' . $model->trace_id
            ]
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
        $this->withoutEvents();
        $response = $this->put(
            route('transaction_requests.error', ['id' => Str::uuid()]),
            [
                'errorInformation' => [
                    'errorCode' => '1234',
                    'errorDescription' => 'errorDescription',
                ]
            ]
        );

        $response->assertStatus(200);
    }
}
