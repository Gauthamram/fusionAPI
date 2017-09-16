<?php 

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Config;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\TipsTicketPrinted;

class LabelHelper extends Printer
{          
	  /**
     * [supplier details of the supplier order]
     * @param [int] $supplier [supplierid]
     */
    public function OrderSupplier($supplierid,$type)
    {
      $supplier = Cache::remember("'".$this->user->getRoleId."-ordersupplier",Carbon::now()->addMinutes(60),function() use ($type) {
        $supplier_query = $this->sql->GetSql('Supplier','',$type);
        if ($this->user->isAdmin() || $this->user->isWarehouse()) {
          $supplier = DB::select($supplier_query);
        } else {
          $supplier = DB::select($supplier_query,[':supplier'=>$this->user->getRoleId()]);
        }
        return $supplier[0];
      });
      return $supplier;
    }

    /**
     * [OrderCartonpack details of the order]
     * @param [int] $order_no [id of the order]
     */       
    public function OrderCartonpack($order_no,$item_number,$listing)
    {
        $cartonpack_query = $this->sql->GetSql('CartonPack','',$item_number);
        // dd($cartonpack_query);
        if (!$item_number) {
          $cartonpacks = DB::select($cartonpack_query,[':order_no'=>$order_no]);    
        } else {
          $cartonpacks = DB::select($cartonpack_query,[':order_no'=>$order_no,'item_number'=>$item_number]);
        }
        
        if ($listing) {
          return $cartonpacks; 
        } else {
          $cartons = $this->CartonDetails($cartonloose);
          return $cartons;
          // $cartonpackdetails = array_map([$this,"CartonDetails"], $cartonpacks);
          // // $this->setCartonSe//quence();
          // return $cartonpackdetails;
        }
    }

    /**
     * [OrderCartonLoose details]
     * @param [int] $order_no [id of the order]
     */
    public function OrderCartonLoose($order_no,$item_number,$listing)
    {
      $cartonloose_query = $this->sql->GetSql('CartonLoose','',$item_number);
        
      if (!$item_number) {
        $cartonloose = DB::select($cartonloose_query,[':order_no'=>$order_no]);    
      } else {
        $cartonloose = DB::select($cartonloose_query,[':order_no'=>$order_no,'item_number'=>$item_number]);
      }
      
      if ($listing) {
        return $cartonloose; 
      } else {
        $cartons = $this->CartonDetails($cartonloose);
        return $cartons;
      }  
    }

    /**
     * [OrderSticky description]
     * @param [int] $order_no [id of the order]
     * @param [string] $type     [type of label required "simplepack,looseitems,ratiopack"]
     */
    public function OrderSticky($order_no,$type)
    {
      $stickies_query = $this->sql->GetSql($type,'','');

      $stickies = DB::select($stickies_query,[':order_no'=>$order_no]);

      $stickydetails = array_map([$this,"StickyDetails"], $stickies);
      
      return $stickydetails;
    }

     /**
     * [fetch all orders of the supplier - approved]
     * @param  [integer] $supplier [id]
     * @return [object collection]           [collection of orders of the supplier]
     */
    public function allOrders($status)
    {
      $orders = Cache::remember("'".$this->user->getRoleId()."-orders",Carbon::now()->addMinutes(60),function() use ($status) {
        $order_query = $this->sql->GetSql('AllOrders', $status);
        
        if ($this->user->isAdmin() || $this->user->isWarehouse()) {
            $supplierorders = DB::select($order_query);
        } else {
          $supplierorders = DB::select($order_query,[':supplier'=>$this->user->getRoleId()]);
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
      $search_query = $this->sql->getSql('SearchOrders','','');
      if ($this->user->isAdmin() || $this->user->isWarehouse()) {
            $searchorders = DB::select($search_query,[':order_no' => $order_no]);
      } else {
          $searchorders = DB::select($search_query,[':order_no' => $order_no,':supplier' => $this->getRoleId()]);
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
            
      $orderdetails_query = $this->sql->GetSql($type,'','');

      $orderdetails = DB::select($orderdetails_query,[':order_no'=>$order_no]);

      return $orderdetails;
    }
}