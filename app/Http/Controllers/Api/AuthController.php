<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyMail;
use Mail;
use Carbon\Carbon;
class AuthController extends Controller
{
   use \App\Traits\PassportToken;
   
   
    /**
     * login api
     * @author Hitesh Tank
     * @return \Illuminate\Http\Response
     */
    public function appLogin(\App\Http\Requests\Api\Auth\LoginRequest $request)
    {
        $requestData = $request->all();
        try {
            
            //return $this->sendResponse(trans('messages.success.login_success'), 200, $requestData);
            if (Auth::guard('web')->attempt(['email' => request('email'), 'password' => request('password')], 1)) {
                $user = Auth::user();
                $success = makeNulltoBlank($user); 

                $data = [
                    'grant_type' => 'password',
                    'client_id' => env('CLIENT_ID'),
                    'client_secret' => env('CLIENT_TOKEN'),
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                    ];
                 //   return $this->sendResponse(trans('messages.success.login_success'), 200, $data); 
                $request = Request::create('/oauth/token', 'POST', $data);
                $response = app()->handle($request);
                $tokenData = json_decode($response->getContent());
                //print_r($tokenData);exit;
                if (!empty($tokenData)) {
                    $updateLastLogin=array();
                    $dt = Carbon::now();
                    $updateLastLogin['last_login_date']=$dt->toDateString();
                    $user->update($updateLastLogin);
                    return $this->sendResponse(trans('messages.success.login_success'), 200, $success)->header('token', $tokenData->access_token)->header('expires_in', $tokenData->expires_in)->header('refresh_token', $tokenData->refresh_token);
                } else {
                    return $this->sendError('Bad Request Error', 400);
                }
                
            } else {
                return $this->sendValidation(['email' => trans('messages.validations.incorrect_login')], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }


}
