<?php

namespace App\Http\Requests\Api\PO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class DeliveryRequest extends FormRequest {

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
            'po_id'      => 'required|exists:purchase_order_master,id',
            'booking_id' => 'required|exists:bookings,id',
        ];
    }

    public
            function messages() {
        return [
            'po_id.exists'        => 'PO does not exist.',
            'po_id.required'      => 'PO does not exists please try again.',
            'booking_id.exists'   => 'Booking does not exists.',
            'booking_id.required' => 'Booking must be required.',
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
