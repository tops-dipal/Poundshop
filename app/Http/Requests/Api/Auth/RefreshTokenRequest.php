<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class RefreshTokenRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public
            function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public
            function rules() {
        return [
            'refresh_token' => 'required',
        ];
    }

    public
            function messages() {
        return [
            'refresh_token.required' => 'Refresh token must be required.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     *
     * @return void
     */
    protected
            function failedValidation(Validator $validator) {

        $errors = $validator->errors();
        throw new HttpResponseException(response()->json([
            'errors'      => $errors,
            'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'status'      => false
                ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

}
