<?php 

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Config;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\TipsTicketPrinted;

class LabelHelper extends ApiHelper
{
    /**
     * [$setting_name - restrict retrieval of only named settings instead of everything]
     * @var string
     */
    protected $setting_name = 'label%';
           
	  /**
     * [supplier details of the supplier order]
     * @param [int] $supplier [supplierid]
     */
    public function OrderSupplier($supplierid,$type)
    {
      $supplier = Cache::remember("'".$this->supplierid."-ordersupplier",Carbon::now()->addMinutes(60),function() use ($type) {
        $supplier_query = $this->sql->GetSql('Supplier','',$type);
        if($this->admin || $this->warehouse){
          $supplier = DB::select($supplier_query);
        } else {
          $supplier = DB::select($supplier_query,[':supplier'=>$this->supplierid]);
        }
        return $supplier[0];
      });
      return $supplier;
    }

    /**
     * [OrderCartonpack details of the order]
     * @param [int] $order_no [id of the order]
     */       
    public function OrderCartonpack($order_no,$item_number,$label)
    {
        $cartonpack_query = $this->sql->GetSql('CartonPack','',$item_number);
        
        if(!$item_number){
          $cartonpacks = DB::select($cartonpack_query,[':order_no'=>$order_no]);    
        } else {
          $cartonpacks = DB::select($cartonpack_query,[':order_no'=>$order_no,'item_number'=>$item_number]);
        }
        
        if(!$label){
           return $cartonpacks; 
        } else {
          $cartonpackdetails = array_map([$this,"CartonDetails"], $cartonpacks);
          $this->setCartonSequence();
          return $cartonpackdetails;
        }
    }

    /**
     * [OrderCartonLoose details]
     * @param [int] $order_no [id of the order]
     */
    public function OrderCartonLoose($order_no,$item_number,$label)
    {
      $cartonloose_query = $this->sql->GetSql('CartonLoose','',$item_number);
        
      if(!$item_number){
          $cartonloose = DB::select($cartonloose_query,[':order_no'=>$order_no]);    
        } else {
          $cartonloose = DB::select($cartonloose_query,[':order_no'=>$order_no,'item_number'=>$item_number]);
        }
      
      if(!$label){
           return $cartonloose; 
      } else {
          $cartonloosedetails = array_map([$this,"CartonDetails"], $cartonloose);
          $this->setCartonSequence();
          return $cartonloosedetails;
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
      $orders = Cache::remember("'".$this->supplierid."-orders",Carbon::now()->addMinutes(60),function() use ($status) {
        $order_query = $this->sql->GetSql('AllOrders', $status);
        
        if($this->admin || $this->warehouse) {
            $supplierorders = DB::select($order_query);
        } else {
          $supplierorders = DB::select($order_query,[':supplier'=>$this->supplierid]);
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
      if($this->admin || $this->warehouse) {
            $searchorders = DB::select($search_query,[':order_no' => $order_no]);
      } else {
          $searchorders = DB::select($search_query,[':order_no' => $order_no,':supplier' => $this->supplierid]);
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