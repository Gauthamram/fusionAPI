<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests;
use Setting;
use JWTAuth;
use Dingo\Api\Exception\InternalHttpException;

class SupplierController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->api = app('Dingo\Api\Dispatcher');
    }

    public function index(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $response = $this->api->raw()->get('suppliers', ['token' => $token]);
                
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
            }
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
            return view('supplier.list')->withErrors($errors)->withTitle('suppliers');
        }
        return view('supplier.list', ['suppliers' => $result['data']])->withTitle('suppliers');
    }

    public function search(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $response = $this->api->raw()->get('supplier/search/'.$request->term, ['token' => $token]);
                
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
            }
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
            return view('supplier.list')->withErrors($errors)->withTitle('suppliers');
        }
        return view('supplier.list', ['suppliers' => $result['data']])->withTitle('suppliers');
    }
}
