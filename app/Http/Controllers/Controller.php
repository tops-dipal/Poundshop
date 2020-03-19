<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public $successStatusCode = 200;
    
    /**
     * 
     * @param type $message
     * @param type $status_code
     * @param type $data
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($message, $status_code, $data = '') {

        $response = [
            'status' => true,
            'status_code' => $status_code,
            'message' => $message,
            'data' => $data,
        ];
        return response()->json($response, $status_code);
    }

    /**

     * return error response.

     *

     * @return \Illuminate\Http\Response

     */
    public function sendError($error, $status_code = 404, $data = '') {

        $response = [
            'status' => false,
            'status_code' => $status_code,
            'message' => $error,
            'data' => $data
        ];
        return response()->json($response, $status_code);
    }

    /**
     * 
     * @param type $error_message
     * @param type $error_code
     * @return type
     */
    public function sendValidation($errors, $error_code, $data = '') {
        return response()->json([
                    'errors' => $errors,
                    'status_code' => $error_code,
                    'status' => false,
                    'data' => $data
                        ], $error_code);
    }
}
