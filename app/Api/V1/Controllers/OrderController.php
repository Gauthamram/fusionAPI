<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Cache;
use Carbon\Carbon;
use App\Fusion\userSetting;
use App\Helpers\labelHelper;
use App\Helpers\OrderHelper;
use App\Fusion\Transformers\OrderTransformer;
use App\Fusion\Transformers\OrderDetailTransformer;
use Config;

class OrderController extends ApiController
{
    use Helpers;

    protected $setting;
    protected $orderTransformer;

    public function __construct(orderTransformer $orderTransformer, userSetting $userSetting, orderdetailTransformer $orderdetailTransformer)
    {
        $this->labelHelper = New LabelHelper($userSetting);
        $this->orderTransformer = $orderTransformer;
        $this->orderdetailTransformer = $orderdetailTransformer; 
        $this->setUserSetting($userSetting);   
    }

    /**
     * List all pending orders that needs to be listed for printing or printed orders from the supplier
     */
    public function index($status = '')
    {    
        $orders = $this->labelHelper->allOrders($status);   
		$data = $this->orderTransformer->transformCollection($orders);
        return 	$this->respond(['data' => $data]);	 					 			
    }

    public function order($order_no)
    {
        $orders = $this->labelHelper->searchOrders($order_no);
        $data = $this->orderTransformer->transformCollection($orders);
        return  $this->respond(['data' => $data]);
    }

    /**
     * [Sticky Label Data ]
     * @param  [type] $order_no [description]
     * @return [type]           [description]
     */
    public function orderdetails($order_no)
    {
        if($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->supplierid) || ($this->admin) || ($this->warehouse)){
                $orders = $this->labelHelper->OrderDetails($order_no,'orderdetails');
                $data = $this->orderdetailTransformer->transformCollection($orders);
                return $this->respond(['data' => $data]);
            }  else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * 
     * [supplier details]
     * @param  [integer] $supplier [description]
     * @return [type]           [description]
     */
    public function supplier($supplier, $type = 'Tickets')
    {
        if (($supplier == $this->supplierid) || ($this->admin) || ($this->warehouse)) {
            if($this->labelHelper->supplierCheck($supplier)){
                $response = $this->labelHelper->OrderSupplier($supplier, $type);
                return $this->respond(['data' => $response]);
            } else{
                return $this->respondNotFound('Supplier Not Found');
            }
        } else {
            return $this->respondForbidden('Forbidden from performing this action');
        }
    }

    /**
     * [cartonpack]
     * @param  [int] $order_no 
     * @return [type]           
     */
    public function cartonpack(Request $request,$order_no = '',$item_number = '',$label = true)
    {
        //if it is post we should return db result by setting label flag to false else return label data
        if($request->isMethod('post')) {
            $label = false;
            $order_no = $request->order_no;
            $item_number = $request->item_number;
        }

        if($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->supplierid) || ($this->admin)){
                if($this->labelHelper->EDICheck($order_no)){
                    $response = $this->labelHelper->OrderCartonpack($order_no,$item_number,$label);
                    return $this->respond(['data' => $response]);
                } else {
                    return $this->respondPreConditionFailed('EDI Order check failed');
                }
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * [cartonloose description]
     * @param  [type] $order_no [description]
     * @return [type]           [description]
     */
    public function cartonloose(Request $request,$order_no = '',$item_number = '',$label = true)
    {
        //if it is post we should return db result by setting label flag to false else return label data
        if($request->isMethod('post')) {
            $label = false;
            $order_no = $request->order_no;
            $item_number = $request->item_number;
        }

        if($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->supplierid) || ($this->admin)){
                if($this->labelHelper->EDICheck($order_no)){
                    $response = $this->labelHelper->OrderCartonloose($order_no,$item_number,$label);
                    return $this->respond(['data' => $response]);
                } else {
                    return $this->respondPreConditionFailed('EDI Order check failed');
                }
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
        
    }

    /**
     * [looseitem description]
     * @param  [type] $order_no [description]
     * @return [type]           [description]
     */
    public function ratiopack($order_no)
    {
        if($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->supplierid) || ($this->admin)){
                $response = $this->labelHelper->OrderSticky($order_no,'RatioPack');
                return $this->respond(['data' => $response]);
            }  else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * [looseitem description]
     * @param  [type] $order_no [description]
     * @return [type]           [description]
     */
    public function looseitem($order_no)
    {
        if($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->supplierid) || ($this->admin)){
                $response = $this->labelHelper->OrderSticky($order_no,'LooseItem');
                return $this->respond(['data' => $response]);
            }  else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * [SimplePack description]
     * @param  [type] $order_no [description]
     * @return [type]           [description]
     */
    public function simplepack($order_no)
    {
        if($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->supplierid) || ($this->admin)){
                $response = $this->labelHelper->OrderSticky($order_no,'SimplePack');
                return $this->respond(['data' => $response]);
            }  else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * [Sticky Label Data ]
     * @param  [type] $order_no [description]
     * @return [type]           [description]
     */
    public function sticky($order_no)
    {
        if($order = $this->labelHelper->orderCheck($order_no)) {
            if (($this->admin) || ($this->warehouse)){
                $response = $this->labelHelper->OrderSticky($order_no,'sticky');
                return $this->respond(['data' => $response]);
            }  else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }
}
