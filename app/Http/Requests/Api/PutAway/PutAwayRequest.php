<?php

namespace App\Http\Requests\Api\PutAway;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class PutAwayRequest extends FormRequest {

    public
            $user;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public
            function authorize() {
        if (Auth::check()) {
            $this->user = Auth::user();
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public
            function rules() {
        return [
            'pallet_location' => 'required',
        ];
    }

    public
            function messages() {
        return [
            'pallet_location.required' => 'Please select pallet location.',
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
