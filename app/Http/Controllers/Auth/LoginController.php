<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Auth;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Lcobucci\JWT\Parser;
use App\User;
use DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    /**
     * User Login
     * @author Hitesh Tank
     * @param Request $request
     * @return type
     * @throws type
     */
    public function login(Request $request)
    {
        try {

           //ini_set('memory_limit', '');
           
            $client = new Client();
            $result = $client->post(apiBaseUrl() . 'login', [
                'form_params' => [
                    'email' => $request->email,
                    'password' => $request->password
                ]
            ]);
            $response = json_decode($result->getBody());
            if ($response->status_code == $this->successStatusCode) {
                $headerToken = $result->getHeaderLine('token');
                $headerRefreshToken = $result->getHeaderLine('refresh_token');
                Session::put('apiToken', $headerToken);
                Session::put('refreshToken', $headerRefreshToken);
                if ($this->attemptLogin($request)) {
                    return $this->sendLoginResponse($request);
                }
            }
            $this->validateLogin($request);
        
            

            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        } catch (RequestException $e) {

        //  dd($e);

            if ($e->hasResponse()) {
                $content = json_decode($e->getResponse()->getBody()->getContents());

                if (!empty($content->errors->{$this->username()})) {
                    $error = is_array($content->errors->{$this->username()})?$content->errors->{$this->username()}[0]:$content->errors->{$this->username()};
                    if (!empty($content->data)) {
                        if (!empty($content->data->link)) {
                            $request->session()->flash('message', $error);
                            return Redirect::to($content->data->link);
                        }
                    }
                    throw ValidationException::withMessages([
                        $this->username() => [$error],
                    ]);
                } else {

                    return $this->sendFailedLoginResponse($request);
                }
            }
        }
    }
    
    
    /**
     * Log the user out of the application.
     * @author Hitesh Tank
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->headers->set('Authorization', 'Bearer ' . Session::get('apiToken'));
        $value = $request->bearerToken();
        if ($value) {
            $id= (new Parser())->parse($value)->getClaims()['jti']->getValue();
            $revoked = DB::table('oauth_access_tokens')->where('id', '=', $id)->update(['revoked' => 1]);
        }
        Session::flush();
        $request->session()->invalidate();
        $this->guard('web')->logout();
        return redirect()->route('login');
    }
}
