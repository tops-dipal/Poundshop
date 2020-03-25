<?php

namespace App\Http\Requests\Api\PO;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory;
use App\PurchaseOrder;

class UpdateRequest extends FormRequest {

    public
            $user;

    /**
     * @author Hitesh Tank
     * @param \App\Http\Requests\Api\PO\Factory $validationFactory
     * If PO is already booked in can't change the status to any others
     */
    public
            function __construct(Factory $validationFactory) {
        $validationFactory->extend('checkPoBooked', function($attribute, $value, $parameters) {
            $purchaseOrderData = PurchaseOrder::find($parameters[0]);

            if (isset($purchaseOrderData->hasBooking) && @count($purchaseOrderData->hasBooking) > 0) {
                if ($purchaseOrderData->po_status > 5) {
                    return TRUE;
                }
                else {
                    return FALSE;
                }
            }
            return TRUE;
        }, 'Sorry. You can\'t change the status, It\'s already Booked in.');
    }

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
            function rules(\Illuminate\Http\Request $request) {
        //dd($request->id);
        return [
            //  'supplier' => 'required',
            'supplier_contact' => 'required',
            'po_status'        => $request->po_status == config('params.po_status.Book In') ? 'required' : 'required|checkPoBooked:' . $request->id,
                // 'po_status'=>'required',
                //     'po_import_type'=>'required',
                //    'supplier_order_number'=>'required',
                //   'recev_warehouse'=>'required',
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
