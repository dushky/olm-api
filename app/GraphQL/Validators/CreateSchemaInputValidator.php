<?php

namespace App\GraphQL\Validators;

use App\Models\Schema;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateSchemaInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('schemas', 'name')
            ],
            'type'=> [
                'required',
                Rule::in(Schema::AVAILABLE_TYPES)
            ],
            'device_type_id' => [
                'required',
                'exists:device_types,id',
            ],
            'software_id' => [
                'required',
                'exists:software,id',
            ],
            'note' => [
                'nullable',
                'string'
            ],
            'arguments' => [
                'array',
            ],
            'arguments.*.name' => [
                'required',
                'max:255',
            ],
            'arguments.*.label' => [
                'required',
                'max:255',
            ],
            'arguments.*.default_value' => [
                'nullable',
                'max:255',
            ],
            'arguments.*.row' => [
                'integer',
                'required'
            ],
            'arguments.*.order' => [
                'integer',
                'required'
            ],
            'arguments.*.options' => [
                'array',
            ],
            'arguments.*.options.*.name' => [
                'required',
                'max:255',
            ],
            'arguments.*.options.*.value' => [
                'required',
                'max:255',
            ],
            'arguments.*.options.*.output_value' => [
                'required',
                'max:255',
            ],
            'schema' => [
                'required',
                'mimetypes:text/xml,application/octet-stream'
            ],
            'preview' => [
                'mimetypes:image/jpg,image/jpeg,image/png',
            ],
        ];
    }
}
