<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests;
use App\TicketRequest;
use Setting;
use JWTAuth;
use Dingo\Api\Exception\InternalHttpException;

class LabelController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->api = app('Dingo\Api\Dispatcher');
    }

    public function createticket(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            foreach ($request->data as $data) {
                $data['ticket_type'] = $request->type;
                $response = $this->api->raw()->post('ticket/create/tips/request?token='.$token, $data);
            }
            if ($response->getstatusCode() == 200) {
                return $this->print_labels($request, $request->type);
            }
        } catch (Exception $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
            return view('labels.list')->withErrors($errors)->withTitle('label_history');
        }
    }

    public function print_labels(Request $request, $type = 'carton')
    {
        try {
            $token = $request->session()->get('token');
            $response = $this->api->raw()->get('tickets', ['token'=>$token,'type'=>$type]);
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
            }
        } catch (Exception $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
            return view('labels.list')->withErrors($errors)->withTitle('label_history');
        }
    }

    public function printcartons(Request $request, int $order_no)
    {
        try {
            $token = $request->session()->get('token');
            $data = array();
            $response = $this->api->raw()->get('order/'.$order_no.'/cartonpack', ['token' => $token]);
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
                $data['cartonpack'] = $result['data'];
            }

            $response =  $this->api->raw()->get('order/'.$order_no.'/cartonloose', ['token' => $token]);
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
                $data['cartonloose'] = $result['data'];
            }
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
                
            return Redirect('portal/label/carton')->withErrors($errors)->withTitle('label_carton')->withInput($request->all());
        }
    }

    public function printstickies(Request $request, int $order_no)
    {
        try {
            $token = $request->session()->get('token');
            $data = array();
            
            $response =  $this->api->raw()->get('order/'.$order_no.'/ratiopack', ['token' => $token]);
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
                $data['ratiopack'] = $result['data'];
            }
            $response =  $this->api->raw()->get('order/'.$order_no.'/simplepack', ['token' => $token]);
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
                $data['simplepack'] = $result['data'];
            }
            $response =  $this->api->raw()->get('order/'.$order_no.'/looseitem', ['token' => $token]);
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
                $data['looseitem'] = $result['data'];
            }
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
                
            return Redirect('portal/label/carton')->withErrors($errors)->withTitle('label_carton')->withInput($request->all());
        }
    }

    public function history(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $page = $request->page ? $request->page : 1;
            $response = $this->api->raw()->get('ticket/tips/printed', ['token' => $token, 'page' => $page]);
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
            }
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
            return view('labels.history')->withErrors($errors)->withTitle('label_history');
        }

        return view('labels.history', ['labels' => $result['data']])->withTitle('label_history');
    }

    public function search(Request $request)
    {
        $token = $request->session()->get('token');

        if ($request->isMethod('post')) {
            try {
                $response = $this->api->raw()->post('order/'.$request->carton_type, ['token' => $token,'order_no'=>$request->order_no,'item_number' => $request->item_number]);
                if ($response->getstatusCode() == 200) {
                    $result = json_decode($response->getContent(), true);
                }
                return view('labels.search', ['orders' => $result['data']])->withTitle('label_carton')->withInput($request->all());
            } catch (InternalHttpException $e) {
                $error = json_decode($e->getResponse()->getContent(), true);
                $errors = [$error['data']['message']];
                return Redirect('portal/label/carton')->withErrors($errors)->withTitle('label_carton')->withInput($request->all());
            }
        } else {
            return view('labels.search')->withTitle('label_carton')->withInput($request->all());
        }
    }

    public function printcartontype(Request $request, string $cartontype, int $order_no, int $item_number = null)
    {
        $token = $request->session()->get('token');

        try {
            $response = $this->api->raw()->get('order/'.$order_no.'/'.$cartontype.'/'.$item_number, ['token' => $token]);
            if ($response->getstatusCode() == 200) {
                $result = json_decode($response->getContent(), true);
                $data[$cartontype] = $result['data'];
            }
            return view('labels.template', ['data' => $data,'format' => 'carton'])->withTitle('label_carton');
        } catch (Exception $e) {
            $error = json_decode($e->getResponse()->getContent(), true);
            $errors = [$error['data']['message']];
            return Redirect('portal/label/carton')->withErrors($errors)->withTitle('label_carton')->withInput($request->all());
        }
    }
}
