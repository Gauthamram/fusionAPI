<?php

namespace App\Api\V1\Controllers;

use JWTAuth;
use Validator;
use Config;
use Log;
use App\User;
use App\ApiSetting;
use App\Events\UserSignUp;
use App\Events\UserPasswordReset;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\ValidationHttpException;
use Cache;

class AuthController extends ApiController
{
    use Helpers;

    /**
     * Returns authenticated user object
     * @return
     */
    public function user()
    {
        if ($currentuser = JWTAuth::parseToken()->authenticate()) {
            $authuser = $currentuser;
        } else {
            $authuser = false;
        }
        return response()->json(compact('authuser'));
    }

    /**
     * User Login and returns token
     * @param  $request
     * @return
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response->errorUnauthorized();
            }
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token', 500);
        }

        Log::info('User logged in : '.$request->email);

        return response()->json(compact('token'));
    }

    /**
     * user registration
     * @param  $request
     * @return
     */
    public function signup(Request $request)
    {
        //$this->user = JWTAuth::parseToken()->authenticate();

        //if ($this->user->isAdmin()) {
            $signupFields = Config::get('boilerplate.signup_fields');
            $hasToReleaseToken = Config::get('boilerplate.signup_token_release');

            $userData = $request->only($signupFields);

            $validator = Validator::make($userData, Config::get('boilerplate.signup_fields_rules'));
        
            if ($validator->fails()) {
                throw new ValidationHttpException($validator->errors()->all());
            }

            //role is Admin set role id to 0
            if ($request->role == Config::get('boilerplate.user_roles.admin')) {
                $role_id = 0;
            } else {
                $role_id = $request->role_id;
            }

            User::unguard();
            $user = new User;
            $user->name = $request->name;
            $user->password = $request->password;
            $user->remember_token = '1';
            $user->email = $request->email;
            $user->roles = $request->role;
            $user->role_id = $role_id;
            $user->save();
            User::reguard();

            if (!$user->id) {
                return $this->response->error('could_not_create_user', 500);
            }

            //call email notification event
            event(new UserSignUp($user));

            if ($hasToReleaseToken) {
                return $this->login($request);
            }
            
            Log::info('New user created by Admin: '.$this->user->email);

            return $this->response->created();
        // } else {
        //     return $this->respondForbidden('Forbidden from performing this action');
        // }
    }

    /**
     * User details Recovery
     * @param  $request
     * @return
     */
    public function recovery(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(Config::get('boilerplate.recovery_email_subject'));
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                Log::info('User password recovery email sent to : '.$request->email);
                return $this->respondSuccess('User password recovery email sent to : '.$request->email);
            case Password::INVALID_USER:
                return $this->response->errorNotFound();
        }
    }

    /**
     * Password reset
     * @param  $request
     * @return
     */
    public function reset(Request $request, $token='')
    {
        if ($request->isMethod('post')) {
            $credentials = $request->only(
            'email',
                'password',
                'password_confirmation',
                'token'
            );

            $validator = Validator::make($credentials, [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
                'password_confirmation' => 'required',
            ]);

            if ($validator->fails()) {
                throw new ValidationHttpException($validator->errors()->all());
            }

            $response = Password::reset($credentials, function ($user, $password) {
                $user->password = $password;
                $user->save();
            });

            Log::info('User password reset by : '.$request->email);

            switch ($response) {
                case Password::PASSWORD_RESET:

                    //call email notification event
                    event(new UserPasswordReset($user));

                    return $this->respondSuccess('Password reset successfull');
                default:
                    return $this->respondWithError('Please check - Invalid '. explode(".", $response)[1]);
            }
        } else {
            return redirect(Config::get('boilerplate.recovery_reset_link').$token);  
        }
    }
}
