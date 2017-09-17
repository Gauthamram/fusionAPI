<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\ValidationHttpException;
use Dingo\Api\Routing\Helpers;
use Config;
use Dingo\Api\Exception\InternalHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticateController extends Controller
{
    protected $authCheck;
    use Helpers;

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $credentials = $request->only(['email', 'password']);

            $validator = Validator::make($credentials, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return view('login')->withInput()->withErrors($errors);
            } else {
                try {
                    $response = $this->api->raw()->post('auth/login', [
                            'email' => $request->email,
                            'password' => $request->password
                        ]);
                    if ($response->getstatusCode() == 200) {
                        $result = json_decode($response->getContent());
                        $request->session()->put('token', $result->token);
                        session(['token'=>$result->token]);
                        return redirect('portal/dashboard');
                    } else {
                        $errors = [$response->getstatusCode()." - ".$response->getstatusText()];
                    }
                } catch (HttpException $e) {
                    $errors = ['Please check your credentials'];
                }
                return view('login')->withErrors($errors);
            }
        } else {
            return view('login');
        }
    }
}
