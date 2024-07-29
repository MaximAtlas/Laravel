<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ApiRequest extends FormRequest
{
    protected function failedAuthorization()
    {
        throw new \HttpResponseException(response()->json([
            'message' => 'Error validation',
            'errors' => Validator::class->getMessageBag(),
        ]));
    }
}
