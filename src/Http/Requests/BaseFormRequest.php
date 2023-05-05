<?php

namespace Pqt2p1\User\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        // if you want, log something here with $this->validationData(), $validator->errors()
        $response =  response()->json([
            'error' => 1,
            'mes' => 'Invalid request data: ' . $validator->errors()->first(),
        ], 422);
        
        throw new ValidationException($validator, $response);
    }
}
