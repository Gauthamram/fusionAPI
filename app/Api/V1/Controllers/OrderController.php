<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Cache;
use Config;
use Log;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Helpers\labelHelper;
use App\Helpers\OrderHelper;
use App\Fusion\Transformers\OrderTransformer;
use App\Fusion\Transformers\OrderDetailTransformer;

class OrderController extends ApiController
{
    protected $orderTransformer;
    public $pagination = false;

    /**
     * __construct
     * @param $orderTransformer
     * @param $orderdetailTransformer
     */
    public function __construct(orderTransformer $orderTransformer, orderdetailTransformer $orderdetailTransformer)
    {
        $this->labelHelper = new LabelHelper();
        $this->orderTransformer = $orderTransformer;
        $this->orderdetailTransformer = $orderdetailTransformer;
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * List all pending orders that needs to be listed for printing or printed orders from the supplier
     * @param  $status
     * @return
     */
    public function index($status = '')
    {
        $orders = $this->labelHelper->allOrders($status);
        $data = $this->orderTransformer->transformCollection($orders, $this->pagination);
        return $this->respond(['data' => $data]);
    }

    /**
     * Returns Order details for the order number
     * @param  $order_no
     * @return
     */
    public function order($order_no)
    {
        $orders = $this->labelHelper->searchOrders($order_no);
        $data = $this->orderTransformer->transformCollection($orders, $this->pagination);
        return  $this->respond(['data' => $data]);
    }

    /**
     * Returns order details
     * @param  $order_no
     * @return
     */
    public function orderdetails($order_no)
    {
        if ($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->user->getRoleId()) || ($this->user->isAdmin()) || ($this->user->isWarehouse())) {
                $orders = $this->labelHelper->orderDetails($order_no, 'orderdetails');
                $data = $this->orderdetailTransformer->transformCollection($orders);
                return $this->respond(['data' => $data]);
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * Returns cartonpack details
     * @param  $order_no
     * @return
     */
    public function cartonpack(Request $request, $order_no = '', $item_number = '', $listing = false)
    {
        //if it is post we should return db result by setting listing flag to true else return label data
        if ($request->isMethod('post')) {
            $listing = true;
            $order_no = $request->order_no;
            $item_number = $request->item_number;
        }

        if ($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->user->getRoleId()) || ($this->user->isAdmin()) || ($this->user->isWarehouse())) {
                if ($this->labelHelper->ediCheck($order_no)) {
                    $response = $this->labelHelper->orderCartonpack($order_no, $item_number, $listing);
                    
                    Log::info('Order Cartonpack retrieved by user  : '.$this->user->email);
                    
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
     * Returns cartonloose details
     * @param  $order_no
     * @return
     */
    public function cartonloose(Request $request, $order_no = '', $item_number = '', $listing = false)
    {
        //if it is post we should return db result by setting listing flag to true else return label data
        if ($request->isMethod('post')) {
            $listing = true;
            $order_no = $request->order_no;
            $item_number = $request->item_number;
        }

        if ($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->user->getRoleId()) || ($this->user->isAdmin()) || ($this->user->isWarehouse())) {
                if ($this->labelHelper->ediCheck($order_no)) {
                    $response = $this->labelHelper->orderCartonloose($order_no, $item_number, $listing);
                    
                    Log::info('Order Cartonloose retrieved by user  : '.$this->user->email);
        
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
     * Returns ratiopack detail
     * @param  $order_no
     * @return
     */
    public function ratiopack($order_no)
    {
        if ($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->user->getRoleId()) || ($this->user->isAdmin()) || ($this->user->isWarehouse())) {
                $response = $this->labelHelper->orderSticky($order_no, 'RatioPack');

                Log::info('Order Ratiopack retrieved by user  : '.$this->user->email);

                return $this->respond(['data' => $response]);
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * Returns looseitem details
     * @param  $order_no
     * @return
     */
    public function looseitem($order_no)
    {
        if ($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->user->getRoleId()) || ($this->user->isAdmin()) || ($this->user->isWarehouse())) {
                $response = $this->labelHelper->orderSticky($order_no, 'LooseItem');

                Log::info('Order looseitem retrieved by user  : '.$this->user->email);

                return $this->respond(['data' => $response]);
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * Returns SimplePack details
     * @param  $order_no
     * @return
     */
    public function simplepack($order_no)
    {
        if ($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->user->getRoleId()) || ($this->user->isAdmin()) || ($this->user->isWarehouse())) {
                $response = $this->labelHelper->orderSticky($order_no, 'SimplePack');

                Log::info('Order simplepack retrieved by user  : '.$this->user->email);

                return $this->respond(['data' => $response]);
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }

    /**
     * Return Sticky Label Data
     * @param  $order_no
     * @return
     */
    public function sticky($order_no)
    {
        if ($order = $this->labelHelper->orderCheck($order_no)) {
            if (($order->supplier == $this->user->getRoleId()) || ($this->user->isAdmin()) || ($this->user->isWarehouse())) {
                $response = $this->labelHelper->orderSticky($order_no, 'sticky');

                Log::info('Order sticky retrieved by user  : '.$this->user->email);

                return $this->respond(['data' => $response]);
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        } else {
            return $this->respondNotFound('Order Not Found');
        }
    }
}
