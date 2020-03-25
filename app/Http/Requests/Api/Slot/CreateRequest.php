<?php

namespace App\Http\Requests\Api\Slot;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use \Illuminate\Http\Request;

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
            function rules(Request $request) {
       
        return [
           /* 'from.*' => ['required',
                Rule::unique('slots')->where(function($query)use($request) {
                            $query->where('from', '=', $request->selected_parent)->whereNull('deleted_at');
                        })
            ],*/
            "from"=>'required',
            'to' => 'required',
                //'barcode'=>'unique:totes_master'
        ];
    }

    public function messages()
    {
        return [
            'from.required' => 'Add more slots to save changes',
            'to.required' => '',
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
