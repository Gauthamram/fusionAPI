<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Config;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\TipsTicketPrinted;
use App\Fusion\Queries\Label\PrintOrder;
use App\Fusion\Queries\Label\SearchOrder;
use App\Fusion\Queries\Label\OrderDetail;
use App\Fusion\Queries\Label\SupplierDetail;
use App\Fusion\Queries\Label\CartonPackOrder;
use App\Fusion\Queries\Label\CartonLooseOrder;
use App\Fusion\Queries\Label\LooseItemOrder;
use App\Fusion\Queries\Label\RatioPackOrder;
use App\Fusion\Queries\Label\SimplePackOrder;

class LabelHelper extends Printer
{
    /**
     * Return details of the supplier
     * @param int supplierid
     * @param string process type
     */
    public function orderSupplier($supplierid, $type = 'Tickets')
    {
        $supplier_detail = new SupplierOrder();
        
        $supplier = Cache::remember("'".$this->user->getRoleId()."-ordersupplier", Carbon::now()->addMinutes(60), function () use ($supplier_detail, $type) {
            $supplier_query = $supplier_detail->query()->filter()->getSql();
        
            if ($this->user->isAdmin() || $this->user->isWarehouse()) {
                $supplier = DB::select($supplier_query, [':type'=>$type]);
            } else {
                $supplier = DB::select($supplier_query, [':supplier'=>$this->user->getRoleId(),':type'=>$type]);
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
    public function orderCartonpack($order_no, $item_number, $listing)
    {
        $cartonpack_order = new CartonPackOrder();

        if (!$item_number) {
            $cartonpack_query = $cartonpack_order->query()->getSql();
            $cartonpacks = DB::select($cartonpack_query, [':order_no'=>$order_no]);
        } else {
            $cartonpack_query = $cartonpack_order->query()->filter($item_number)->getSql();
            $cartonpacks = DB::select($cartonpack_query, [':order_no'=>$order_no,':item_number'=>$item_number]);
        }
        
        if ($listing || (empty($cartonpacks))) {
            return $cartonpacks;
        } else {
            $cartons = $this->cartonDetails($cartonpacks);
            return $cartons;
        }
    }

    /**
     * Returns OrderCartonLoose details
     * @param int  id of the order
     * @param int  item number
     * @param boolean to get list of db results
     */
    public function orderCartonLoose($order_no, $item_number, $listing)
    {
        $cartonloose_order = new CartonLooseOrder();

        if (!$item_number) {
            $cartonloose_query = $cartonloose_order->query()->union()->getSql();
            $cartonloose = DB::select($cartonloose_query, [':order_no'=>$order_no]);
        } else {
            $cartonloose_query = $cartonloose_order->query()->filter($item_number)->union()->filter($item_number)
                                    ->getSql();
            $cartonloose = DB::select($cartonloose_query, [':order_no'=>$order_no,':item_number'=>$item_number]);
        }
      
        if ($listing || (empty($cartonloose))) {
            return $cartonloose;
        } else {
            $cartons = $this->cartonDetails($cartonloose);
            return $cartons;
        }
    }

    /**
     * Returns OrderSticky details
     * @param int id of the order
     * @param string type of label required "simplepack,looseitems,ratiopack"
     */
    public function orderSticky($order_no, $type)
    {
        switch (strtolower($type)) {
            case 'simplepack':
                $sticky_order = new SimplePackOrder();
                break;
            
            case 'ratiopack':
                $sticky_order = new RatioPackOrder();
                break;

            default:
                $sticky_order = new LooseItemOrder();
                break;
        }

        $stickies_query = $sticky_order->query()->getSql();

        $stickies = DB::select($stickies_query, [':order_no'=>$order_no]);

        $stickydetails = $this->stickyDetails($stickies);
      
        return $stickydetails;
    }

    /**
    * Return collection of orders
    * @param  integer supplierid
    */
    public function allOrders($status)
    {
        $print_order = new PrintOrder();

        $orders = Cache::remember("'".$this->user->getRoleId()."-orders", Carbon::now()->addMinutes(60), function () use ($print_order,$status) {
            if ($this->user->isAdmin() || $this->user->isWarehouse()) {
                $order_query = $print_order->query()->filter($status)->getSql();
                $supplierorders = DB::select($order_query, [
                                    ':supplier_trait'=> Config::get('ticket.supplier_trait')
                                ]);
            } else {
                $order_query = $print_order->query()->forSupplier()->filter()->getSql();
                $supplierorders = DB::select($order_query, [
                                    ':supplier'=>$this->user->getRoleId(),
                                    ':supplier_trait'=> Config::get('ticket.supplier_trait')
                                ]);
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
        $search_order = new SearchOrder();

        if ($this->user->isAdmin() || $this->user->isWarehouse()) {
            $search_query = $search_order->query()->filter()->getSql();
            $searchorders = DB::select($search_query, [':order_no' => $order_no]);
        } else {
            $search_query = $search_order->query()->forSupplier()->filter()->getSql();
            $searchorders = DB::select($search_query, [
                                ':order_no' => $order_no,
                                ':supplier' => $this->getRoleId(),
                                ':supplier_trait' => Config::get('ticket.supplier_trait')
                            ]);
        }
        return $searchorders;
    }

    /**
     * Returns Orderdetails for label types - only for warehouse labels
     * @param int  order number
     * @param type process type
     */
    public function orderDetails($order_no, $type)
    {
        $order_detail = new OrderDetail();
        $orderdetails_query = $order_detail->query()->getSql();

        $orderdetails = DB::select($orderdetails_query, [':order_no'=>$order_no]);

        return $orderdetails;
    }
}
