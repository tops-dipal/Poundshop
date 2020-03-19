<?php

namespace App\Http\Requests\Api\Common;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
class CreateRequest extends FormRequest
{
    public $user;

    public static $roles_array =array();

    public static $message_array=array();

    /**
     * Determine if the user is authorized to make this request.
     * @author : Shubham Dayma
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
    public function rules() {
        return self::$roles_array;
    }

     public function messages() {
        return self::$message_array;
    }

    /**
     * Handle a failed validation attempt.
     * @author : Shubham Dayma
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
