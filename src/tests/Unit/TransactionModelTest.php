<?php

namespace Tests\Unit;

use App\Models\Transaction;
use Tests\TestCase;

class TransactionModelTest extends TestCase
{
    /**
     * Test creating with valid data.
     *
     * @return void
     */
    public function testStoreValidData()
    {
        $model = factory(Transaction::class)->create();
        $this->assertInstanceOf(Transaction::class,$model);
    }

    /**
     * Test updating with valid data.
     *
     * @return void
     */
    public function testUpdateValidData()
    {
        $model = factory(Transaction::class)->create();
        $this->assertTrue($model->update(factory(Transaction::class)->make()->attributesToArray()));
    }
}
