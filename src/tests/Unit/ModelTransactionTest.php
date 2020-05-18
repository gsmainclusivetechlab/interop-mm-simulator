<?php

namespace Tests\Unit;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ModelTransactionTest extends TestCase
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
     * Test creating with invalid data.
     *
     * @return void
     */
    public function testStoreInvalidData()
    {
        $emptyModel = factory(Transaction::class)->make(self::emptyData());
        $this->assertFalse(Validator::make($emptyModel->attributesToArray(), self::rules())->passes());

        $validationFailedModel = factory(Transaction::class)->make(self::invalidData());
        $this->assertFalse(Validator::make($validationFailedModel->attributesToArray(), self::rules())->passes());

        $existModel = factory(Transaction::class)->create();
        $modelUniqueTraceId = factory(Transaction::class)->make([
            'trace_id' => $existModel->trace_id,
        ]);
        $this->assertFalse(Validator::make($modelUniqueTraceId->attributesToArray(), self::rules())->passes());
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

    /**
     * Test updating with invalid data.
     *
     * @return void
     */
    public function testUpdateInvalidData()
    {
        $modelWithEmptyData = factory(Transaction::class)->create();
        $modelWithEmptyData->setRawAttributes(self::emptyData());
        $this->assertFalse(Validator::make($modelWithEmptyData->attributesToArray(), self::rules())->passes());

        $modelWithInvalidData = factory(Transaction::class)->create();
        $modelWithInvalidData->setRawAttributes(self::invalidData());
        $this->assertFalse(Validator::make($modelWithInvalidData->attributesToArray(), self::rules())->passes());

        $existTraceId = factory(Transaction::class)->create();
        $modelWithUniqueEmail = factory(Transaction::class)->create();
        $modelWithUniqueEmail->trace_id = $existTraceId->trace_id;
        $this->assertFalse(Validator::make($modelWithUniqueEmail->attributesToArray(), self::rules())->passes());
    }

    /**
     * Database validation rules.
     *
     * @return array
     */
    protected static function rules()
    {
        return [
            'trace_id' => ['required', 'string', 'max:32', 'unique:transactions'],
            'callback_url' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:23'],
            'currency' => ['required', 'string', 'max:3'],
            'type' => ['required', 'string', 'max:256'],
            'subType' => ['string', 'max:255'],
            'descriptionText' => ['string', 'max:160'],
            'requestDate' => ['integer'],
            'requestingOrganisationTransactionReference' => ['string', 'max:256'],
            'geoCode' => ['string', 'max:256'],
            'debitParty' => ['required', 'string'],
            'creditParty' => ['required', 'string'],
            'originalTransactionReference' => ['string', 'max:256'],
            'servicingIdentity' => ['string', 'max:256'],
            'transactionStatus' => ['required', Rule::in(Transaction::STATUSES)],
            'transactionReceipt' => ['string', 'max:256'],
        ];
    }

    /**
     * Empty Data.
     *
     * @return array
     */
    protected static function emptyData()
    {
        return [
            'trace_id' => null,
            'callback_url' => null,
            'amount' => null,
            'currency' => null,
            'type' => null,
            'debitParty' => null,
            'creditParty' => null,
            'transactionStatus' => null,
        ];
    }

    /**
     * Invalid Data.
     *
     * @return array
     */
    protected static function invalidData()
    {
        return [
            'trace_id' => Str::random(500),
            'callback_url' => Str::random(500),
            'amount' => Str::random(500),
            'currency' => Str::random(500),
            'type' => Str::random(500),
            'subType' => Str::random(500),
            'descriptionText' => Str::random(500),
            'requestingOrganisationTransactionReference' => Str::random(500),
            'geoCode' => Str::random(500),
            'debitParty' => 123,
            'creditParty' => 123,
            'originalTransactionReference' => Str::random(500),
            'servicingIdentity' => Str::random(500),
            'transactionStatus' => Str::random(500),
            'transactionReceipt' => Str::random(500),
        ];
    }
}
