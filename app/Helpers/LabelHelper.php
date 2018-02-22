<?php 

namespace App\Helpers;

use Config;
use Cache;
use Carbon\Carbon;
use App\TipsTicketPrinted;
use App\Order;
use App\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * Returns OrderCartonpack details of the order
     * @param int  id of the order
     * @param int  item number
     * @param boolean  to get list of db results
     */
    public function orderCartonpack($order_no, $item_number = '', $listing = false)
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
            $packcartons = $this->cartonDetails($cartonpacks);

            foreach ($packcartons as $pack) {
            
                $cartonpackdetail['cartonpackdetail'] = array(
                    'style' => $pack->style,
                    'packnumber' => $pack->item,
                    'packtype' => $pack->pack_type,
                    'description' => $pack->description,
                    'group' => $pack->group_name,
                    'dept' => $pack->department_name,
                    'class' => $pack->class_name,
                    'subclass' => $pack->sub_class_name
                    );

                foreach ($pack->carton_details as $barcode) {
                    $cartonpackdetail['cartonpackdetail']['barcodes'][]['barcode'] = [
                        'productindicatorbarcode' => $barcode['pibarcode'],
                        'productindicator' => $barcode['pinumber'],
                        'cartonbarcode' => $barcode['barcode'],
                        'carton' => $barcode['number']
                    ]; 
                }   
                $cartonpackdetails[] = $cartonpackdetail;         
            }

            return $cartonpackdetails;
        }
    }

    /**
     * Returns OrderCartonLoose details
     * @param int  id of the order
     * @param int  item number
     * @param boolean to get list of db results
     */
    public function orderCartonLoose($order_no, $item_number = '', $listing = false)
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
            $loosecartons = $this->cartonDetails($cartonloose);

            foreach ($loosecartons as $loose) {
                $cartonloosedetail['cartonloosedetail'] = array(
                    'style' => $loose->style,
                    'itemnumber' => $loose->item,
                    'description' => $loose->description,
                    'colour' => $loose->colour,
                    'size' => $loose->item_size,
                    'cartonquantity' => $loose->piquantity,
                    'printquantity' => 1,
                );

               foreach ($loose->carton_details as $barcode) {
                    $cartonloosedetail['cartonloosedetail']['barcodes'][]['barcode'] = [
                        'productindicatorbarcode' => $barcode['pibarcode'],
                        'productindicator' => $barcode['pinumber'],
                        'cartonbarcode' => $barcode['barcode'],
                        'carton' => $barcode['number']
                    ]; 
                }
                $cartonloosedetails[] = $cartonloosedetail;  
            }

            return $cartonloosedetails;
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

        $stickydetail = [];
        
        $stickies_query = $sticky_order->query()->getSql();

        $stickies = DB::select($stickies_query, [':order_no'=>$order_no]);

        $stickydetails = $this->stickyDetails($stickies);

        foreach ($stickydetails as $detail) {
            $stickydetail[]['stickydetail'] = [
                'style' => $detail->style,
                'itemnumber' => $detail->item,
                'description' => $detail->description,
                'colour' => $detail->colour,
                'size' => $detail->item_size,
                'stockroomlocator' => $detail->stockroomlocator,
                'itembarcode' => $detail->barcode,
                'itembarcodetype' => $detail->barcode_type,
                'printquantity' => $detail->printquantity
            ]; 
        }
      
        return $stickydetail;
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
                $order_query = $print_order->query()->forAdmin()->getSql();
                $supplierorders = DB::select($order_query, [
                                    ':supplier_trait'=> Config::get('ticket.supplier_trait')
                                ]);
            } else {
                $order_query = $print_order->query()->forSupplier()->getSql();
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
                                ':supplier' => $this->user->getRoleId(),
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

    /**
     *Returns order label data as requested format
     */
    public function orderData($order_no, $format)
    {
        $supplier = Order::findOrFail($order_no)->supplier()->first();
        $address = $supplier->address()->country()->first();
        
        $orderdetails['ordernumber'] = $order_no;
        
        $supplier_details['id'] = $supplier->supplier;
        $supplier_details['name'] = $supplier->sup_name;
        $supplier_details['contact_name'] = $supplier->contact_name;
        $supplier_details['contact_phone'] = $supplier->contact_phone;
        $supplier_details['contact_fax'] = $supplier->contact_fax;
        $supplier_details['contact_email'] = $supplier->contact_email;
        $supplier_details['address1'] = $address->add_1;
        $supplier_details['address2'] = $address->add_2;
        $supplier_details['address3'] = $address->add_3;
        $supplier_details['city'] = $address->city;
        $supplier_details['state'] = $address->state;
        $supplier_details['country'] = $address->country_desc;

        $data['orderdetails'] = $orderdetails;
        $data['supplier'] = $supplier_details;

        $data['cartonpackdetails'] = $this->orderCartonpack($order_no);
          
        $data['cartonloosedetails'] = $this->orderCartonLoose($order_no);

        $data['stickydetails'][] = $this->orderSticky($order_no, 'RatioPack');      
      
        $data['stickydetails'][] = $this->orderSticky($order_no, 'LooseItem');

        $data['stickydetails'][] = $this->orderSticky($order_no, 'SimplePack');

        $this->dataformat->setFormatter($format);

        $return_data = $this->dataformat->format($data);
        return $return_data;
    }
}
