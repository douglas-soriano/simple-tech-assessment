<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
 use Illuminate\Contracts\Validation\Validator;
 use Illuminate\Http\Exceptions\HttpResponseException;

use App\Helpers\ApiResponse;
use Illuminate\Http\Response;

class UpdateFundRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'start_year' => 'sometimes|digits:4|integer',
            'fund_manager_id' => 'sometimes|exists:funds_managers,id',
            'aliases' => 'sometimes|array'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(ApiResponse::error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)));
    }
}
