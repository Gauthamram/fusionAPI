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
     * Return details of the supplier
     * @param int supplierid
     * @param string process type
     */
    public function OrderSupplier($supplierid, $type)
    {
        $supplier = Cache::remember("'".$this->user->getRoleId."-ordersupplier", Carbon::now()->addMinutes(60), function () use ($type) {
            $supplier_query = $this->sql->GetSql('Supplier', '', $type);
        
            if ($this->user->isAdmin() || $this->user->isWarehouse()) {
                $supplier = DB::select($supplier_query);
            } else {
                $supplier = DB::select($supplier_query, [':supplier'=>$this->user->getRoleId()]);
            }
            return $supplier[0];
        });
        return $supplier;
    }

    /**
     * Returns OrderCartonpack details of the order
     * @param int  id of the order
     * @param int  item number
     * @param boolean  to get list of db results
     */
    public function OrderCartonpack($order_no, $item_number, $listing)
    {
        $cartonpack_query = $this->sql->GetSql('CartonPack', '', $item_number);
        // dd($cartonpack_query);
        if (!$item_number) {
            $cartonpacks = DB::select($cartonpack_query, [':order_no'=>$order_no]);
        } else {
            $cartonpacks = DB::select($cartonpack_query, [':order_no'=>$order_no,'item_number'=>$item_number]);
        }
        
        if ($listing || (empty($cartonpacks))) {
            return $cartonpacks;
        } else {
            $cartons = $this->CartonDetails($cartonpacks);
            return $cartons;
        }
    }

    /**
     * Returns OrderCartonLoose details
     * @param int  id of the order
     * @param int  item number
     * @param boolean to get list of db results
     */
    public function OrderCartonLoose($order_no, $item_number, $listing)
    {
        $cartonloose_query = $this->sql->GetSql('CartonLoose', '', $item_number);
        
        if (!$item_number) {
            $cartonloose = DB::select($cartonloose_query, [':order_no'=>$order_no]);
        } else {
            $cartonloose = DB::select($cartonloose_query, [':order_no'=>$order_no,'item_number'=>$item_number]);
        }
      
        if ($listing || (empty($cartonloose))) {
            return $cartonloose;
        } else {
            $cartons = $this->CartonDetails($cartonloose);
            return $cartons;
        }
    }

    /**
     * Returns OrderSticky details
     * @param int id of the order
     * @param string type of label required "simplepack,looseitems,ratiopack"
     */
    public function OrderSticky($order_no, $type)
    {
        $stickies_query = $this->sql->GetSql($type, '', '');

        $stickies = DB::select($stickies_query, [':order_no'=>$order_no]);

        $stickydetails = $this->StickyDetails($stickies);
      
        return $stickydetails;
    }

    /**
    * Return collection of orders
    * @param  integer supplierid
    */
    public function allOrders($status)
    {
        $orders = Cache::remember("'".$this->user->getRoleId()."-orders", Carbon::now()->addMinutes(60), function () use ($status) {
            $order_query = $this->sql->GetSql('AllOrders', $status);
        
            if ($this->user->isAdmin() || $this->user->isWarehouse()) {
                $supplierorders = DB::select($order_query);
            } else {
                $supplierorders = DB::select($order_query, [':supplier'=>$this->user->getRoleId()]);
            }
            return $supplierorders;
        });
        return $orders;
    }

    /**
     * Returns collections - order collection or array from sql query
     * @param  int number of the order
     *
     */
    public function searchOrders($order_no)
    {
        $search_query = $this->sql->getSql('SearchOrders', '', '');
        if ($this->user->isAdmin() || $this->user->isWarehouse()) {
            $searchorders = DB::select($search_query, [':order_no' => $order_no]);
        } else {
            $searchorders = DB::select($search_query, [':order_no' => $order_no,':supplier' => $this->getRoleId()]);
        }
        $searchorders = DB::select($search_query, [':order_no' => $order_no]);
        return $searchorders;
    }

    /**
     * Returns Orderdetails for label types - only for warehouse labels
     * @param int  order number
     * @param type process type
     */
    public function OrderDetails($order_no, $type)
    {
        $orderdetails_query = $this->sql->GetSql($type, '', '');

        $orderdetails = DB::select($orderdetails_query, [':order_no'=>$order_no]);

        return $orderdetails;
    }
}
