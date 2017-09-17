<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests;
use Setting;
use JWTAuth;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;
use Dingo\Api\Exception\InternalHttpException;

class HomeController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->api = app('Dingo\Api\Dispatcher');
    }
    
    /**
     * [dashboard]
     * @param  Request $request
     * @return
     */
    public function dashboard(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $response = $this->api->raw()->get('orders/pending', ['token' => $token]);
            $tickets_response = $this->api->raw()->get('ticket/tips/printed', ['token' => $token]);

            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
            }

            if ($tickets_response->getstatusCode() == 200) {
                $order_result = json_decode($tickets_response->getContent(), true);
            }
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
            return view('dashboard')->withErrors($errors)->withTitle('dashboard');
        }
                      
        //return dshboard with user details
        //summary of all
        return view('dashboard', ['orders' => $result['data'],'labels' => $order_result['data']])->withTitle('dashboard');
    }

    /**
     * [orderlist]
     * @param  Request $request
     * @return
     */
    

    public function setting(Request $request)
    {
        //get user account from user
        $currentuser = JWTAuth::parseToken()->authenticate();
        
        if ($request->isMethod('post')) {
            $credentials = $request->only(
            'email',
                'password',
                'password_confirmation'
            );

            $validator = Validator::make($credentials, [
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return view('account.edit')->withErrors($errors)->withTitle('setting');
            }

            try {
                $token = $request->session()->get('token');
                dd($token);
                $request->merge(['token' => $token]);
                $response = $this->api->raw()->post('auth/reset', $request->only('email', 'password', 'password_confirmation', 'token'));
                if ($response->getstatusCode() == 200) {
                    $result = json_decode($response->getContent(), true);
                }
                 
                dd($result);
            } catch (InternalHttpException $e) {
                $error = json_decode($e->getResponse()->getContent(), true);
                $errors = [$error['data']['message']];
                return view('account.edit')->withErrors($errors)->withTitle('dashboard');
            }
        } else {
            return view('account.edit')->withTitle('setting');
        }
    }
}
