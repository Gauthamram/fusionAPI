<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Config;
use App\Order;
use App\Carton;
use App\Sticky;
use App\Supplier;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use JWTAuth;

class Printer
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Returns boolean based on order existence
     * @param int order number
     */
    public function orderCheck(int $order_no)
    {
        if ($cache_order = Cache::get("'".$order_no."-order'", false)) {
            return $cache_order;
        } else {
            $order = Order::find($order_no);
            if ($order) {
                Cache::put("'".$order_no."-order", $order, 60);
                return $order;
            } else {
                Cache::put("'".$order_no."-order", false, 60);
                return false;
            }
        }
    }

    /**
     * Returns boolean based on supplier existence
     * @param int order number
     */
    public function supplierCheck(int $supplier)
    {
        if ($cache_supplier = Cache::get("'".$supplier."-supplier'", false)) {
            return $cache_supplier;
        } else {
            $supplier = Supplier::find($supplier);
            if ($supplier) {
                Cache::put("'".$supplier."-supplier", $supplier, 60);
                return $supplier;
            } else {
                Cache::put("'".$supplier."-supplier", false, 60);
                return false;
            }
        }
    }

    /**
   * Returns boolean based on CheckPackIndicator
   * @param object ticket object
   */
    public function checkPackIndicator($ticket)
    {
        $packindicator = 'none';

        if ($ticket->pack_ind == 'Y') {
            $packindicator = 'transport';
            config::get('ticket.packindicator.transport');
            if ($ticket->simple_pack_ind == 'Y') {
                $packindicator = 'simple';
                config::get('ticket.packindicator.simple');
            }
        }

        return $packindicator;
    }

    /**
     * Returns carton details as an array
     * @param objectcollection carton objects from the collections
     */
    public function cartonDetails($cartons)
    {
        $cartondetails = array();
        $cartons = Carton::hydrate($cartons);
      
        foreach ($cartons as $carton) {
            $carton->setCartonDetails();
            $carton->setProductIndicator();
            $cartondetails[] = $carton;
        }

        return $cartondetails;
    }

    /**
     * Returns StickyDetails of the order
     * @param objectcollection sticky objects from the collection
     */
    public function stickyDetails($stickies)
    {
        $stickydetails = array();
        $stickies = Sticky::hydrate($stickies);

        foreach ($stickies as $key => $sticky) {
            $sticky->setStickyData();
            $stickydetails[] = $sticky;
        }

        return $stickydetails;
    }

    /**
     * Returns boolean based on checkEDI
     * @param  int order number
     */
    public function ediCheck($order_no)
    {
        if ($cache_value = Cache::get("'".$order_no."-isEDI", false)) {
            return $cache_value;
        } else {
            $order = Order::find($order_no);
        }
        if ($order->edi_po_ind == config::get('ticket.edi')) {
            Cache::put("'".$order_no."-isEDI", true, 60);
            return true;
        } else {
            Cache::put("'".$order_no."-isEDI", false, 60);
            return false;
        }
    }
}
