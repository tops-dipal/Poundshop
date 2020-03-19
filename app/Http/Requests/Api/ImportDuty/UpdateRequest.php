<?php

namespace App\Http\Requests\Api\ImportDuty;

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
            
            'commodity_code_id' => 'required|unique_with:import_duty,country_id,' . $request->id ,'country_id,deleted_at,NULL','rate'=>'required',
        
        ];
    }
    public function messages()
    {
        return ['unique_with' => 'This combination of commodity code, country  already exists.'];
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
