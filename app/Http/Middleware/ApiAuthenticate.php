<?php

namespace App\Http\Middleware;

use Closure;

/**
* ApiAuthenticatecheck as route middleware
*/
class ApiAuthenticate
{
	/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($token = $request->session()->get('token')){
            try {
                $response = app('Dingo\Api\Dispatcher')->raw()->get('auth/user',['token' => $token]);
                if($response->getstatusCode() == 200){
                    $result = json_decode($response->getContent(), true);
                    if(!$result['authuser']){
                    	return redirect('login');
                    }
                }                 
            } catch (\Dingo\Api\Exception\InternalHttpException $e) {
                return redirect('login');               
            }
		} else {
			return redirect('login');
		}

		\View::share('user', $result['authuser']);
        return $next($request);
    }
}