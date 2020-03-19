<?php

namespace App\Http\Requests\Api\Mapping;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
class MappingRequest extends FormRequest
{
    public $user;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::check()){
            $this->user = Auth::user();
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'range_id'=>'required|unique_with:category_mappings,magento_category_id',
            'magento_category_id'=>'required',

            //'purchase_order_id'=>'required|exists:purchase_order_master,id'
        ];
    }
    
    
    public function messages()
    {
        return [
            'unique_with' => 'Mapping already exists.',
            'range_id.required'=>'Please Select Buying Range',
            'magento_category_id.required'=>'Please Select Magento Selling Category',
            // 'purchase_order_id.exists' => 'PO does not exists.',
            // 'purchase_order_id.required'=>'Please select proper PO.'
        ];
    }
    
    
     /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     *
     * @return void
     */
    protected function failedValidation(Validator $validator) {

        $errors = $validator->errors();
        throw new HttpResponseException(response()->json([
            'errors' => $errors,
            'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'status' => false
                ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
