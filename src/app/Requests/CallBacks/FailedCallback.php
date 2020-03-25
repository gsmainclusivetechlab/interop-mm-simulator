<?php

namespace App\Requests\CallBacks;

class FailedCallback extends BaseCallback
{
	protected function collectData(): array
	{
		return [
			'errorCategory' => 'businessRule',
			'errorCode' => 'genericError'
		];
	}

	protected function collectHeaders(): array
	{
		return [];
	}
}