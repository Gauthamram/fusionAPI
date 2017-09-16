<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests;
use Setting;
use JWTAuth;
use Dingo\Api\Exception\InternalHttpException;

class OrderController extends Controller
{
    use Helpers;

    public function __construct()
    {
    	$this->api = app('Dingo\Api\Dispatcher');
    }

	public function orderlist(Request $request)
    {   
        try 
        {
            $token = $request->session()->get('token');

            if($request->isMethod('post')){
                $response = $this->api->raw()->get('order/'.$request->order_no,['token' => $token]);                    
            } else {
                $response = $this->api->raw()->get('orders/',['token' => $token]);
            }

            if($response->getstatusCode() == 200){
                $result = json_decode($response->getContent(),true);
            }    
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(),true);
            $errors = [$error['data']['message']];  
            return view('labels.list')->withErrors($errors)->withTitle('label_history');               
        }

		return view('labels.list',['orders' => $result['data']])->withTitle('label_orders');
    }

    public function search(Request $request)
    {
        $token = $request->session()->get('token');

    	if($request->isMethod('post')){
            try 
            {
                $response = $this->api->raw()->post('order/'.$request->carton_type,['token' => $token,
                    'order_no'=>$request->order_no,'item_number' => $request->item_number]);
                if($response->getstatusCode() == 200){
                    $result = json_decode($response->getContent(),true);
                }    
                return view('labels.search',['orders' => $result['data']])->withTitle('label_carton')->withInput($request->all());
            } catch (InternalHttpException $e) {
                $error = json_decode($e->getResponse()->getContent(),true);
                $errors = [$error['data']['message']];
                return Redirect('portal/label/carton')->withErrors($errors)->withTitle('label_carton')->withInput($request->all());
            }
    	} else {

    		return view('labels.search')->withTitle('label_carton')->withInput($request->all());	
    	}
    	
    }

    public function orderdetails(Request $request, int $order_no)
    {
        try 
        {
            $token = $request->session()->get('token');
            $data = array(); 
            $response = $this->api->raw()->get('order/details/'.$order_no,['token' => $token]);
                if($response->getstatusCode() == 200){
                    $result = json_decode($response->getContent(),true);
                    $data['orderdetails'] = $result['data'];
                } 
            $response = $this->api->raw()->post('order/cartonpack',['token' => $token,'order_no' => $order_no]);
                if($response->getstatusCode() == 200){
                    $result = json_decode($response->getContent(),true);
                    $data['cartonpack'] = $result['data'];
                }  
            $response =  $this->api->raw()->post('order/cartonloose',['token' => $token,'order_no' => $order_no]);
                if($response->getstatusCode() == 200){
                    $result = json_decode($response->getContent(),true);
                    $data['cartonloose'] = $result['data'];
                }    
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(),true);
            $errors = [$error['data']['message']];  
            return view('labels.options',['orderdetails' => $data,'order_no' => $order_no])->withTitle('label_orders');             
        }
        return view('labels.options',['orderdetails' => $data,'order_no' => $order_no])->withTitle('label_orders');
    }

}
