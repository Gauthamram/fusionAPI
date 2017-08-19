<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class TokenEntrustAbility extends BaseMiddleware
{
    public function handle($request, Closure $next, $roles, $permissions, $validateAll = false)
    {
        // if(!$token = $request->token){
        //     return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
        // }
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
        }


        // try {
        //     $response = app('Dingo\Api\Dispatcher')->raw()->get('auth/user',['token' => $token]);
        //         if($response->getstatusCode() == 200){
        //             $result = json_decode($response->getContent(), true);
        //             if(!$user = $result['authuser']){
        //                return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404); 
        //             }
        //         }
        // } catch (\Dingo\Api\Exception\InternalHttpException $e) {
        //     return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        // }
        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
            return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        }

        if (! $user) {
            return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
        }

       if (!$request->user()->ability(explode('|', $roles), explode('|', $permissions), array('validate_all' => $validateAll))) {
            return $this->respond('tymon.jwt.invalid', 'token_invalid', 401, 'Unauthorized');
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}