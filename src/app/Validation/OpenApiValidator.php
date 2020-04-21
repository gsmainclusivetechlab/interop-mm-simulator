<?php

namespace App\Validation;

use App\Enums\ApiTypeEnum;
use App\Traits\ParseContentType;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\Reader;
use cebe\openapi\spec\Schema;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use League\OpenAPIValidation\PSR7\Exception\Validation\InvalidBody;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use League\OpenAPIValidation\Schema\Exception\KeywordMismatch;
use League\OpenAPIValidation\Schema\Exception\SchemaMismatch;
use League\OpenAPIValidation\Schema\Keywords\Required;
use League\OpenAPIValidation\Schema\SchemaValidator;
use Nyholm\Psr7\ServerRequest;
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
    protected Schema $bodySchema;
    protected SchemaValidator $validator;

    /**
     * OpenApiValidator init.
     *
     * @param ApiTypeEnum $api
     * @param Request $request
     * @throws \Exception
     */
    public function init(ApiTypeEnum $api, Request $request)
    {
        $this->request = $request;
        $this->spec = $this->getSpec($api);

        return $this;
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

        $validator = (new ValidatorBuilder)->fromSchema($this->spec)
            ->getServerRequestValidator();

        try {
            $validator->validate(new ServerRequest(
                $this->request->method(),
                $this->request->fullUrl(),
                $this->request->headers->all(),
                $this->request->getContent(),
            ));
        } catch (InvalidBody $e) {
            $this->bodySchema = Arr::get(
                $this->spec->paths[$this->request->getPathInfo()]->{$this->getRequestMethod()}->requestBody->content,
                $this->parseContentType()
            )->schema;

            $this->validateSchema($this->getData(), $this->bodySchema);
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

        if (empty($this->bodySchema)) {
            return $results;
        }

        foreach ($this->bodySchema->properties as $key) {
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
     * Validates schema
     *
     * @return void
     */
    public function validateSchema($data, Schema $schema, string $attribute = null)
    {
        try {
            $validator = new SchemaValidator(SchemaValidator::VALIDATE_AS_REQUEST);

            try {
                $validator->validate($data, $schema);
            } catch (KeywordMismatch $e) {
                if ($e->keyword() === 'required') {
                    $this->validateRequiredInScheme($schema, $attribute);
                }

                if (!is_array($data)) {
                    $this->addError($attribute, $e->getMessage());
                    return;
                }

                if ($schema->type === 'array') {
                    $this->validateProperties($data, $schema->items->properties, $attribute . '.*.');

                    if (Arr::isAssoc($data)) {
                        $this->validateProperties($data, $schema->properties, $attribute ? $attribute . '.' : null);
                        return;
                    }

                    foreach ($data as $item) {
                        $this->validateProperties($item, $schema->properties, $attribute ? $attribute . '.' : null);
                    }

                    return;
                }

                $this->validateProperties($data, $schema->properties, $attribute ? $attribute . '.' : null);
            } catch (ValidationFailed $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        } catch (TypeErrorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * Validates existing of all required properties in the scheme
     *
     * @return void
     */
    protected function validateRequiredInScheme(Schema $schema, string $parent = null)
    {
        foreach ($schema->required as $attribute) {
            try {
                (new Required($schema, SchemaValidator::VALIDATE_AS_REQUEST))
                    ->validate($this->getData(), [$attribute]);
            } catch (KeywordMismatch $e) {
                $this->addError($parent . $attribute, $e->getMessage());
            }
        }
    }

    /**
     * Validates all properties
     *
     * @return void
     */
    protected function validateProperties(array $data, array $properties, string $parent = null)
    {
        foreach ($properties as $attribute => $property) {
            if (!($field = Arr::get($data, $attribute))) {
                continue;
            }

            if (!is_array($field) || Arr::isAssoc($field)) {
                $this->validateSchema($field, $property, $parent . $attribute);
                continue;
            }

            foreach ($field as $item) {
                $this->validateSchema($item, $property, $parent . $attribute);
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
