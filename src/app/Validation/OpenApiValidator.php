<?php

namespace App\Validation;

use App\Enums\ApiTypeEnum;
use App\Traits\ParseContentType;
use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\Reader;
use cebe\openapi\spec\Schema;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidBody;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\Schema\Exception\KeywordMismatch;
use League\OpenAPIValidation\Schema\Exception\SchemaMismatch;
use League\OpenAPIValidation\Schema\Keywords\Required;
use League\OpenAPIValidation\Schema\SchemaValidator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class OpenApiValidator
 * @package App\Http
 */
class OpenApiValidator extends Validator
{
    use ParseContentType;

    protected Request $request;
    protected SpecObjectInterface $spec;
    protected Schema $requestSchema;
    protected SchemaValidator $validator;

    /**
     * OpenApiValidator constructor.
     *
     * @param ApiTypeEnum $api
     * @param Request $request
     * @throws \Exception
     */
    public function __construct(ApiTypeEnum $api, Request $request)
    {
        $this->request = $request;
        $this->spec = $this->getSpec($api);

        parent::__construct(resolve(Translator::class), $this->getData(), []);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->request->all();
    }

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes(): bool
    {
        $this->messages = new MessageBag();

        $validator = (new \League\OpenAPIValidation\PSR7\ValidatorBuilder)->fromSchema($this->spec)
            ->getServerRequestValidator();

        try {
            $validator->validate(new \Nyholm\Psr7\ServerRequest(
                $this->request->method(),
                $this->request->fullUrl(),
                $this->request->headers->all(),
                $this->request->getContent(),
            ));
        } catch (InvalidBody $e) {
            $this->validateBody();
        } catch (ValidationFailed $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Here we will spin through all of the "after" hooks on this validator and
        // fire them off. This gives the callbacks a chance to perform all kinds
        // of other validation that needs to get wrapped up in this operation.
        foreach ($this->after as $after) {
            $after();
        }

        return $this->messages->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function validated(): array
    {
        if ($this->invalid()) {
            throw new ValidationException($this);
        }

        $results = [];

        $missingValue = Str::random(10);

        if (empty($this->requestSchema)) {
            return $results;
        }

        foreach ($this->requestSchema->properties as $key) {
            $value = data_get($this->getData(), $key, $missingValue);

            if ($value !== $missingValue) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function failed(): array
    {
        return $this->messages->all();
    }

    /**
     * Get specification based on YAML
     *
     * @param ApiTypeEnum $api
     * @return SpecObjectInterface
     * @throws \Exception
     */
    protected function getSpec(ApiTypeEnum $api): SpecObjectInterface
    {
        $cachedItem = "openapi.{$api->value}";
        $yamlFile = app_path("OpenApi/{$api->value}.yaml");

        $yamlFileModifiedAt = filemtime($yamlFile);

        if (Arr::get(cache($cachedItem),'modified') !== $yamlFileModifiedAt) {
            cache()->forever($cachedItem, [
                'spec' => Reader::readFromYamlFile($yamlFile),
                'modified' => $yamlFileModifiedAt,
            ]);
        }

        return Arr::get(cache($cachedItem), 'spec');
    }

    /**
     * Validates request body
     *
     * @return void
     */
    public function validateBody()
    {
        try {
            $this->requestSchema = Arr::get(
                $this->spec->paths[$this->request->getPathInfo()]->{$this->getRequestMethod()}->requestBody->content,
                $this->parseContentType()
            )->schema;

            $validator = new SchemaValidator(SchemaValidator::VALIDATE_AS_REQUEST);

            try {
                $validator->validate($this->getData(), $this->requestSchema);
            } catch (KeywordMismatch $e) {
                if ($e->keyword() === 'required') {
                    $this->validateRequiredAll();
                }

                $this->validateProperties();
            } catch (ValidationFailed $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        } catch (TypeErrorException $e) {
        }
    }

    /**
     * Validates existing of all required properties
     *
     * @return void
     */
    protected function validateRequiredAll()
    {
        foreach($this->requestSchema->required as $required) {
            try {
                (new Required($this->requestSchema, SchemaValidator::VALIDATE_AS_REQUEST))
                    ->validate($this->getData(), [$required]);
            } catch (KeywordMismatch $e) {
                $this->addError($required, $e->getMessage());
            }
        }
    }

    /**
     * Validates all properties
     *
     * @return void
     */
    protected function validateProperties()
    {
        $validator = new SchemaValidator(SchemaValidator::VALIDATE_AS_REQUEST);

        foreach ($this->requestSchema->properties as $attribute => $property) {
            if (!($field = Arr::get($this->getData(), $attribute))) {
                continue;
            }

            try {
                $validator->validate($field, $property);
            } catch (KeywordMismatch $e) {
                $this->addError($attribute, $e->getMessage());
            } catch (SchemaMismatch $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        }
    }

    /**
     * @return string
     */
    protected function getRequestMethod(): string
    {
        return Str::lower($this->request->method());
    }

    /**
     * Add an error message to the collection.
     *
     * @param  string  $attribute
     * @param  string  $message
     *
     * @return void
     */
    protected function addError(string $attribute, string $message)
    {
        $this->messages->add($attribute, $message);
    }
}
