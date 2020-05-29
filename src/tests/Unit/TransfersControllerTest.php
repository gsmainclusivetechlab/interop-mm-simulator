<?php

namespace Tests\Unit;

use App\Http\Requests\TransferCreate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransfersControllerTest extends TestCase
{
    /**
     * Test update.
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->withoutEvents();
        $request = new TransferCreate();
        $completedTimestamp = (new Carbon())->toIso8601ZuluString(
            'millisecond'
        );
        $data = $request->mapInTo($completedTimestamp);
        $response = $this->put(
            route('transfers.update', ['id' => Str::uuid()]),
            $data
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
            route('transfers.error', ['id' => Str::uuid()]),
            [
                'errorInformation' => [
                    'errorCode' => '1234',
                    'errorDescription' => 'errorDescription',
                ],
            ]
        );

        $response->assertStatus(200);
    }
}
