<?php

namespace App\Http\Requests\Api\PO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class CreateRequest extends FormRequest {

    public
            $user;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public
            function authorize() {
        $this->user = Auth::user();
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
            'supplier' => 'required',
            'supplier_contact' => 'required',
            'po_number' => 'required',
            'po_status' => 'required',
            "hidden_country" => "required",
            // 'po_import_type'=>'required',
//            'supplier_order_number'=>'required',
            'recev_warehouse' => 'required',
        ];
    }

    public
            function messages() {
        return [
            'hidden_country.required' => 'Please select country.',
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
                    'errors' => $errors,
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'status' => false
                        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

}
