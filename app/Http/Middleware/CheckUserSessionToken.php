<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use DateTime;

class CheckUserSessionToken {

    protected $server;
    protected $tokens;

    public function __construct(ResourceServer $server, TokenRepository $tokens) {
        $this->server = $server;
        $this->tokens = $tokens;
    }

    /**
     * Handle an incoming request.
     * @author Hitesh Tank
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
          
        $request->headers->set('Authorization', 'Bearer ' . Session::get('apiToken'));

        $isAuthenticated = $this->validateToken($request, true);
       
        if (!$isAuthenticated) {
            return redirect()->route('logout');
        }
        $response = $next($request);
        return $response;
    }

    /**
     * @author Hitesh Tank
     * @param Request $request
     * @param type $localCall
     * @return boolean
     */
    public function validateToken(Request $request, $localCall = false) {
        // First, we will convert the Symfony request to a PSR-7 implementation which will
        // be compatible with the base OAuth2 library. The Symfony bridge can perform a
        // conversion for us to a Zend Diactoros implementation of the PSR-7 request.
        $psr = (new DiactorosFactory)->createRequest($request);

        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);

            // Next, we will assign a token instance to this user which the developers may use
            // to determine if the token has a given scope, etc. This will be useful during
            // authorization such as within the developer's Laravel model policy classes.
            $token = $this->tokens->find(
                    $psr->getAttribute('oauth_access_token_id')
            );
            $currentDate = new DateTime();
            $tokenExpireDate = new DateTime($token->expires_at);

            $isAuthenticated = $tokenExpireDate > $currentDate ? true : false;

            if ($localCall) {
                return $isAuthenticated;
            } else {
                return json_encode(array('authenticated' => $isAuthenticated));
            }
        } catch (OAuthServerException $e) {
            if ($localCall) {
                return false;
            } else {
                return json_encode(array('error' => 'Something went wrong with authenticating. Please logout and login again.'));
            }
        }
    }

}
