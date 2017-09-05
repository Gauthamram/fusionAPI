<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests;
use App\TicketRequest;
use Setting;
use JWTAuth;
use Dingo\Api\Exception\InternalHttpException;
use PDF;

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
                $response = $this->api->raw()->post('ticket/create/tips/request?token='.$token,$data);
            }
                if($response->getstatusCode() == 200){
                    return $this->print_labels($request, $request->type);
                }  
        } catch (Exception $e) {
             $error = json_decode($e->getResponse()->getContent(),true);
             $errors = [$error['data']['message']];  
             return view('labels.list')->withErrors($errors)->withTitle('label_history');
        }
    }

    public function print_labels(Request $request, $type = 'carton')
    {
        try {
            $token = $request->session()->get('token');
            $response = $this->api->raw()->get('tickets',['token'=>$token,'type'=>$type]);
            if($response->getstatusCode() == 200){
                $result = json_decode($response->getContent(),true);
            } 
        } catch (Exception $e) {
            $error = json_decode($e->getResponse()->getContent(),true);
            $errors = [$error['data']['message']];  
            return view('labels.list')->withErrors($errors)->withTitle('label_history');
        }
    }

	public function printcartons(Request $request,$order_no)
	{
		try {
            // $token = $request->session()->get('token');
            // $data = array();
            // $response = $this->api->raw()->get('order/'.$order_no.'/cartonpack',['token' => $token]);
            //     if($response->getstatusCode() == 200){
            //         $result = json_decode($response->getContent(),true);
            //         $data['cartonpack'] = $result['data'];
            //     }  

            // $response =  $this->api->raw()->get('order/'.$order_no.'/cartonloose',['token' => $token]);
            //     if($response->getstatusCode() == 200){
            //         $result = json_decode($response->getContent(),true);
            //         $data['cartonloose'] = $result['data'];
            //     }  
            $data['cartonpack'][0] = array(
                    'ordernumber' => '1087717',
                    'printquantity' => '1', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'packnumber' => '113245741',
                    'packtype'=> 'A',
                    'group'=> 'Accessories',
                    'dept' =>'Accessories',
                    'class' =>'Bags',
                    'subclass'=> 'Casual',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
                $data['cartonpack'][1] = array(
                    'ordernumber' => '1087717',
                    'printquantity' => '1', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'packnumber' => '113245741',
                    'packtype'=> 'A',
                    'group'=> 'Accessories',
                    'dept' =>'Accessories',
                    'class' =>'Bags',
                    'subclass'=> 'Casual',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
                $data['cartonpack'][2] = array(
                    'ordernumber' => '1087717',
                    'printquantity' => '1', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'packnumber' => '113245741',
                    'packtype'=> 'A',
                    'group'=> 'Accessories',
                    'dept' =>'Accessories',
                    'class' =>'Bags',
                    'subclass'=> 'Casual',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
                $data['cartonpack'][3] = array(
                    'ordernumber' => '1087717',
                    'printquantity' => '1', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'packnumber' => '113245741',
                    'packtype'=> 'A',
                    'group'=> 'Accessories',
                    'dept' =>'Accessories',
                    'class' =>'Bags',
                    'subclass'=> 'Casual',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
                $data['cartonloose'][] = array(
                    'ordernumber' => '1087717',
                    'cartonquantity' => '6', 
                    'style' => '112379490',
                    'description' => 'wts:KYLIEBAG2:8pk:multi',
                    'productindicator' => '(00) 193327670001155097',
                    'productindicatorbarcode' => '00193327670001155097',
                    'itemnumber' => '113245741',
                    'size'=> '1SIZ',
                    'colour'=> 'BLK~Black',
                    'carton' => array(
                        array(
                        'number' => '(400) 1087717 (02) 113245741 (30) 1',
                        'barcode' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                        ),
                    ),
                  );
    
                  $pdf = PDF::loadView('labels.template',['data' => $data,'format' => 'carton'])->setPaper('a6');
                  return $pdf->download('invoice.pdf');
              // return view('labels.template',['data' => $data,'format' => 'carton'])->withTitle('label_carton');        
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(),true);
                $errors = [$error['data']['message']];
                
                return Redirect('portal/label/carton')->withErrors($errors)->withTitle('label_carton')->withInput($request->all());
        }
	}

    public function printstickies(Request $request,$order_no)
    {
        try {
            // $token = $request->session()->get('token');
            // $data = array();
            
            // $response =  $this->api->raw()->get('order/'.$order_no.'/ratiopack',['token' => $token]);
            //     if($response->getstatusCode() == 200){
            //         $result = json_decode($response->getContent(),true);
            //         $data['ratiopack'] = $result['data'];
            //     }
            // $response =  $this->api->raw()->get('order/'.$order_no.'/simplepack',['token' => $token]);
            //     if($response->getstatusCode() == 200){
            //         $result = json_decode($response->getContent(),true);
            //         $data['simplepack'] = $result['data'];
            //     }
            // $response =  $this->api->raw()->get('order/'.$order_no.'/looseitem',['token' => $token]);
            //     if($response->getstatusCode() == 200){
            //         $result = json_decode($response->getContent(),true);
            //         $data['looseitem'] = $result['data'];
            //     } 
            $data['ratiopack'][] = array(
                    'itemnumber'    => '112805541',
                    'description1'  => '0103LV34919',
                    'description2'  => 'GRN~Sierre XL',
                    'size'          => 'XL',
                    'stockroomlocator' => '347015',
                    'barcode'       => '400001561726',
                    'barcodetype' => 'ean13'
                    );
                $data['simplepack'][] = array(
                    'itemnumber'    => '112805541',
                    'description1'  => '0103LV34919',
                    'description2'  => 'GRN~Sierre XL',
                    'size'          => 'XL',
                    'stockroomlocator' => 'PACK 6',
                    'barcode'       => '400001561726',
                    'barcodetype' => 'ean13'
                    );
                $data['looseitem'][] = array(
                    'itemnumber'    => '112805541',
                    'description1'  => '0103LV34919',
                    'description2'  => 'GRN~Sierre XL',
                    'size'          => 'XL',
                    'stockroomlocator' => '347015',
                    'barcode'       => '400001561726',
                    'barcodetype' => 'ean13'
                    );
                $data['simplepack'][] = array(
                    'itemnumber'    => '112805541',
                    'description1'  => '0103LV34919',
                    'description2'  => 'GRN~Sierre XL',
                    'size'          => 'XL',
                    'stockroomlocator' => 'PACK 6',
                    'barcode'       => '400001561726',
                    'barcodetype' => 'ean13'
                    );
                $data['looseitem'][] = array(
                    'itemnumber'    => '112805541',
                    'description1'  => '0103LV34919',
                    'description2'  => 'GRN~Sierre XL',
                    'size'          => 'XL',
                    'stockroomlocator' => '347015',
                    'barcode'       => '400001561726',
                    'barcodetype' => 'ean13'
                    );
                    $data['simplepack'][] = array(
                    'itemnumber'    => '112805541',
                    'description1'  => '0103LV34919',
                    'description2'  => 'GRN~Sierre XL',
                    'size'          => 'XL',
                    'stockroomlocator' => 'PACK 6',
                    'barcode'       => '400001561726',
                    'barcodetype' => 'ean13'
                    );
                $data['looseitem'][] = array(
                    'itemnumber'    => '112805541',
                    'description1'  => '0103LV34919',
                    'description2'  => 'GRN~Sierre XL',
                    'size'          => 'XL',
                    'stockroomlocator' => '347015',
                    'barcode'       => '400001561726',
                    'barcodetype' => 'ean13'
                    );        
                  $pdf = PDF::loadView('labels.template',['data' => $data,'format' => 'sticky']);
                  return $pdf->download('invoice.pdf');
             // return view('labels.template',['data' => $data,'format' => 'sticky'])->withTitle('label_carton');        
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(),true);
                $errors = [$error['data']['message']];
                
                return Redirect('portal/label/carton')->withErrors($errors)->withTitle('label_carton')->withInput($request->all());
        }
    }

    public function history(Request $request)
    {
    	try 
        {
    		$token = $request->session()->get('token');
            $response = $this->api->raw()->get('ticket/tips/printed',['token' => $token]);
            if($response->getstatusCode() == 200){
                $result = json_decode($response->getContent(),true);
            }    
        } catch (InternalHttpException $e) {
            $error = json_decode($e->getResponse()->getContent(),true);
            $errors = [$error['data']['message']];  
            return view('labels.history')->withErrors($errors)->withTitle('label_history');             
        }

        return view('labels.history',['labels' => $result['data']])->withTitle('label_history');
    }

    public function search(Request $request)
    {
        $token = $request->session()->get('token');

    	if($request->isMethod('post')){
            try 
            {
                $response = $this->api->raw()->post('order/'.$request->carton_type,['token' => $token,'order_no'=>$request->order_no,'item_number' => $request->item_number]);
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

    public function printcartontype(Request $request,$cartontype, $order_no, $item_number = '')
    {
        $token = $request->session()->get('token');

        try {

            $response = $this->api->raw()->get('order/'.$order_no.'/'.$cartontype.'/'.$item_number,['token' => $token]);
                if($response->getstatusCode() == 200){
                    $result = json_decode($response->getContent(),true);
                    $data[$cartontype] = $result['data'];
                }  
                // dd($data);
                // $data['cartonloose'][] = array(
                //     'ordernumber' => '1087717',
                //     'cartonquantity' => '6', 
                //     'style' => '112379490',
                //     'description' => 'wts:KYLIEBAG2:8pk:multi',
                //     'productindicatorbarcode' => '(00) 193327670001155097',
                //     'productindicator' => '00193327670001155097',
                //     'itemnumber' => '113245741',
                //     'size'=> '1SIZ',
                //     'colour'=> 'BLK~Black',
                //     'carton' => array(
                //         array(
                //         'barcode' => '(400) 1087717 (02) 113245741 (30) 1',
                //         'number' => str_replace(array(')','(',' '),"",'(400) 1087717 (02) 113245741 (30) 1'),
                //         ),
                //     ),    
                //   );
                // return view('labels.template',['data' => $data,'format' => 'carton'])->withTitle('label_carton');  
                $pdf = PDF::loadView('labels.template',['data' => $data,'format' => 'carton'])->setPaper('a6');
                  return $pdf->download('invoice.pdf');
                // return view('labels.template',['data' => $data,'format' => 'carton'])->withTitle('label_carton');  
        } catch (Exception $e) {
            $error = json_decode($e->getResponse()->getContent(),true);
                $errors = [$error['data']['message']];
                return Redirect('portal/label/carton')->withErrors($errors)->withTitle('label_carton')->withInput($request->all());
        }
    }
}
