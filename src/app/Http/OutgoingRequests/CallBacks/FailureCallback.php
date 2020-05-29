<?php

namespace App\Requests\CallBacks;

class FailureCallback extends BaseCallback
{
    protected function collectData(): array
    {
        return [
            'errorCategory' => 'businessRule',
            'errorCode' => 'genericError',
        ];
    }

    protected function collectHeaders(): array
    {
        return [
            'traceparent' => request()->header('traceparent'),
        ];
    }
}
