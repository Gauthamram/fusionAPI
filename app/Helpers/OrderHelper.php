<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Config;
use App\Helpers\SqlHelper;
use App\Order;
use Cache;
use Carbon\Carbon;
use App\Fusion\UserSetting;
use Illuminate\Support\Facades\Log;

class OrderHelper
{
	/**
     * [__construct]
     */
    public function __construct(userSetting $userSetting)
    {
      $this->admin = $userSetting->isAdmin();
      $this->warehouse = $userSetting->isWarehouse();
      $this->sqlHelper = New sqlHelper($userSetting);

      $this->userSetting = $userSetting->getuserSetting(); 

    }

    /**
     * [fetch all orders of the supplier - approved]
     * @param  [integer] $supplier [id]
     * @return [object collection]           [collection of orders of the supplier]
     */
    public function allOrders($status)
    {
      $orders = Cache::remember("'".$this->userSetting['number']."-orders",Carbon::now()->addMinutes(60),function() use ($status) {
        $order_query = $this->sqlHelper->GetSql('AllOrders', $status);
        
        if($this->admin || $this->warehouse) {
            $supplierorders = DB::select($order_query);
        } else {
          $supplierorders = DB::select($order_query,[':supplier'=>$this->userSetting['number']]);
        }
        return $supplierorders;
      });
      return $orders;
    }

    /**
     * [searchOrders - search and list orders]
     * @param  [int] $order_no [number of the order]
     * @return [collections]           [order collection or array from sql query]
     */
    public function searchOrders($order_no)
    {
      $search_query = $this->sqlHelper->getSql('SearchOrders','','');
      if($this->admin || $this->warehouse) {
            $searchorders = DB::select($search_query,[':order_no' => $order_no]);
      } else {
          $searchorders = DB::select($search_query,[':order_no' => $order_no,':supplier' => $this->userSetting['number']]);
      }
      $searchorders = DB::select($search_query,[':order_no' => $order_no]);
      return $searchorders;
    }

    /**
     * [Orderdetails for label types - only for warehouse labels]
     * @return [object] [orderdetails]
     */
    public function OrderDetails($order_no,$type)
    {
      			
      $orderdetails_query = $this->sqlHelper->GetSql($type,'','');

      $orderdetails = DB::select($orderdetails_query,[':order_no'=>$order_no]);

      return $orderdetails;
    }

    /**
     * [OrderCheck for existence]
     * @param [int] $order_no [order number]
     */
    public function OrderCheck($order_no)
    {    
        if($cache_order = Cache::get("'".$order_no."-order'", false)){
          return $cache_order;
        } else{
          $order = Order::find($order_no);
          if($order) {
            Cache::put("'".$order_no."-order",$order,60);
              return $order; 
          } else {
            Cache::put("'".$order_no."-order",false,60);
              return false;
          }
        }
    }

}