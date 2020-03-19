<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use App\User;
use DB;
use Validator;
class ForgotpasswordController extends Controller
{
    use SendsPasswordResetEmails;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:api');
    }
    
     /**
     *
     * @return type
     */
    public function broker()
    {
        return Password::broker('users');
    }
    
    /**
     * Send password reset link
     * @author Hitesh Tank
     * @param Request $request
     * @return type
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
        $user_check = User::where('email', $request->email)->first();
        $errorMsg = '';
        if (empty($user_check)) {
            $errorMsg = 'Email id does not exist with us.';
        }
//        } elseif (isset($user_check)) {
//            if ($user_check->is_active == 0) {
//                $errorMsg = trans('messages.common.you_are_deactivate_by_admin');
//            }
//        }
        if (!empty($errorMsg)) {
                return $this->sendValidation(['email' => $errorMsg], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        if ($response === Password::RESET_LINK_SENT) {
            return $this->sendResponse(trans('passwords.sent'), 200);
        }
    }
}
