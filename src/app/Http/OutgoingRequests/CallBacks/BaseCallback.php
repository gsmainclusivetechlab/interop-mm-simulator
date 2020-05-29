<?php

namespace App\Requests\CallBacks;

use App\Models\Transaction;
use App\Requests\BaseRequest;
use Illuminate\Support\Env;

/**
 * Class BaseCallback
 *
 * @package App\Requests
 */
abstract class BaseCallback extends BaseRequest
{
    /**
     * @var Transaction
     */
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;

        parent::__construct(
            $this->collectData(),
            $this->collectHeaders(),
            Env::get('HOST_SERVICE_PROVIDER') . $transaction->callback_url
        );

        $this->method = 'PUT';
    }

    abstract protected function collectData(): array;
    abstract protected function collectHeaders(): array;
}
