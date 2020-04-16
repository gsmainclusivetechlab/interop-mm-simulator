<?php

namespace App\Validation;

/**
 * Class OpenApiValidatorFactory
 * @package App\Validation
 */
class OpenApiValidatorFactory extends \Illuminate\Validation\Factory
{
    /**
     * Resolve a new OpenApiValidator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return OpenApiValidator
     */
    protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
    {
        return new OpenApiValidator($this->translator, $data, $rules, $messages, $customAttributes);
    }
}
