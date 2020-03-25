<?php

namespace App\Http\Requests\Api\Mapping;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
     public $user;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->user = Auth::user();
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(\Illuminate\Http\Request $request) {
        return [
            
            'range_id' => 'required|unique_with:category_mappings,magento_category_id,' . $request->id ,'magento_category_id,deleted_at,NULL','magento_category_id'=>'required',
        
        ];
    }
    public function messages()
    {
        return ['unique_with' => 'Mapping already exists.'];
    }
     protected function failedValidation(Validator $validator) {

        $errors = $validator->errors();
        throw new HttpResponseException(response()->json([
            'errors' => $errors,
            'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'status' => false
                ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
