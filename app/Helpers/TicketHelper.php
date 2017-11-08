<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Config;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\TipsTicketPrinted;
use App\Fusion\Queries\Ticket\CartonLooseTicket;
use App\Fusion\Queries\Ticket\CartonPackTicket;
use App\Fusion\Queries\Ticket\DeleteLooseCarton;
use App\Fusion\Queries\Ticket\ItemTicket;
use App\Fusion\Queries\Ticket\PackTicket;
use App\Fusion\Queries\Ticket\RequestTicket;
use App\Fusion\Queries\Ticket\SimplePackTicket;

class TicketHelper extends Printer
{
    /**
     * [OrderTipsTicketData create tips ticket for the order]
     * @param [type] $order_no    [number of the order]
     * @param [type] $item_number [item number]
     */
    public function tipsTicketData($order_no, $item_number)
    {
        //get ticket request
        $ticket_request = new RequestTicket();
        $ticketrequest_query = $ticket_request->query()->getSql();
        $ticketrequests = DB::select($ticketrequest_query, [
                            ':order_no' => $order_no,
                            ':item_number' => $item_number
                        ]);

        //delete all bad requests for the loosecartons
        $deleteloosecarton = $this->deleteLooseCarton($order_no);

        foreach ($ticketrequests as $ticketrequest) {
            if ($ticketrequest->ticket_type_id == config::get('ticket.type.carton')) {
                $data['cartonpack'][] = $cartonpackdata = $this->ticketRequestCartonPack($ticketrequest);
                $data['cartonloose'][] = $cartonloosedata = $this->ticketRequestCartonPack($ticketrequest);
            } else {
                $thePackIndicator = $this->checkPackIndicator($ticketrequest);
          
                switch ($thePackIndicator) {
                    case 'none':
                      $data['sticky'][] = $stickydata = $this->ticketRequestItem($ticketrequest);
                      break;
                    case 'simple':
                      $data['sticky'][] = $stickydata = $this->ticketRequestSimplePack($ticketrequest);
                      break;
                    case 'transport':
                      $data['sticky'][] = $stickydata = $this->ticketRequestPack($ticketrequest);
                      break;
                    default:
                      $data['sticky'];
                      break;
                }
            }
        }
        return $data;
    }

    /**
     * [TicketRequestCartonLoose - Cartonloose data]
     * @param [type] $ticket [ticketrequest]
     */
    public function ticketRequestCartonLoose($ticket)
    {
        $cartonloose_ticket = new CartonLooseTicket();
        $cartonloose_query = $cartonloose_ticket->query()->getSql();
        $cartonloose = DB::select($cartonloose_query, [':order_no' => $ticket->order_no]);
      
        $cartonloosedetails = $this->cartonDetails($cartonloose);

        return $cartonloosedetails;
    }

    /**
     * [TicketRequestCartonPack Cartonpack data]
     * @param [type] $ticket [ticketrequest]
     */
    public function ticketRequestCartonPack($ticket)
    {
        $cartonpack_ticket = new CartonPackTicket();
        $cartonpack_query = $cartonpack_ticket->query()->getSql();
        $cartonpacks = DB::select($cartonpack_query, [':order_no' => $ticket->order_no]);
      
        $cartonpackdetails = $this->cartonDetails($cartonpacks);

        return $cartonpackdetails;
    }

    /**
     * [TicketRequestItem Item data]
     * @param [type] $ticket [ticketrequest]
     */
    public function ticketRequestItem($ticket)
    {
        $item_ticket = new ItemTicket();
        $orderitem_query = $item_ticket->query()->getSql();
        $orderitems = DB::select($orderitem_query, [':order_no' => $ticket->order_no,':item_number' => $ticket->item_number,':location1' => $ticket->location,
            ':location2' => $ticket->location, ':location3' => $ticket->location]);
        $prev_item = '';
        foreach ($orderitems as $orderitem) {
            if ($prev_item != $orderitem->item_number) {
                $prev_item = $orderitem->item_number;

                $itemdata = array(
                    'order_number' => $ticket->order_no,
                    'item' => $orderitem->item_number,
                    'stockroomlocator' => $orderitem->stockroom,
                    'description' => $orderitem->short_desc,
                    'colour' => $orderitem->colour,
                    'size' => $orderitem->item_size,
                    'quantity' => $ticket->quantity,
                    'barcode' => $orderitem->barcode
                  );
            }
        }
        return $itemdata;
    }

    /**
     * [TicketRequestSimplePack SimplePack data]
     * @param [type] $ticket [ticketrequest]
     */
    public function ticketRequestSimplePack($ticket)
    {
        $simplepack_ticket = new SimplePackTicket();
        $prev_item = '';
        $i =1;

        //get simple pack items
        $ordersimplepack_query = $simplepack_ticket->query()->getSql();
        $ordersimplepacks = DB::select($ordersimplepack_query, [':order_no' => $ticket->order_no,':packnumber' => $ticket->item_number,':location1' => $ticket->location,':location2' => $ticket->location,':location3' => $ticket->location]);

        if ($ticket->sort_order_type == config::get('ticket.sort_type.packandloose')) {
            $packquantity = $ticket->quantity;
            $requestquantity = 1;
        } else {
            //loose sort order - qty = simplepack quantity * requested quantity
            $packquantity = 1;
            $requestquantity = $ticket->quantity;
        }

        $i=1;

        while ($i <= $packquantity) {
            foreach ($ordersimplepacks as $key => $ordersimplepack) {
                if ($prev_item != $ordersimplepack->item_number) {
                    $prev_item = $ordersimplepack->item_number;

                    $simplepackdata[$i][$key] = array(

                      'order_number' => $orderpack->order_no,
                      'item' => $orderpack->item_number,
                      'stockroomlocator' => $orderpack->stockroom,
                      'description' => $orderpack->short_desc,
                      'colour' => $orderpack->colour,
                      'size' => $orderpack->item_size,
                      'quantity' => $orderpack->quantity * $requestquantity,
                      'barcode' => $orderpack->barcode
                    );
                }
            }
            $i++;
        }
        return $simplepackdata;
    }

    /**
     * [TicketRequestPack Pack data]
     * @param [type] $ticket [ticketrequest]
     */
    public function ticketRequestPack($ticket)
    {
        $prev_item = '';
        $i =1;

        $pack_ticket = new PackTicket();
        //get pack items
        $orderpack_query = $pack_ticket->query()->getSql();
        $orderpacks = DB::select($orderpack_query, [':order_no' => $ticket->order_no,':packnumber' => $ticket->item_number,':location1' => $ticket->location,
        ':location2' => $ticket->location,':location3' => $ticket->location]);

        if ($ticket->sort_order_type == config::get('ticket.sort_type.packandloose')) {
            $packquantity = $ticket->quantity;
            $requestquantity = 1;
        } else {
            //loose sort order - qty = pack quantity * requested quantity
            $packquantity = 1;
            $requestquantity = $ticket->quantity;
        }

        while ($i <= $packquantity) {
            foreach ($orderpacks as $key => $orderpack) {
                if ($prev_item != $orderpack->item_number) {
                    $prev_item = $orderpack->item_number;

                    $packdata[$i][$key] = array(
                      'order_number' => $orderpack->order_no,
                      'item' => $orderpack->item_number,
                      'stockroomlocator' => $orderpack->stockroom,
                      'description' => $orderpack->short_desc,
                      'colour' => $orderpack->colour,
                      'size' => $orderpack->item_size,
                      'quantity' => $orderpack->quantity * $requestquantity,
                      'barcode' => $orderpack->barcode
                    );
                }
            }
            $i++;
        }
        return $packdata;
    }

    /**
     * [DeleteLooseCarton - Delete any request that is loose and qty is 0]
     * @param [type] $ticket [ticketrequest object]
     */
    public function deleteLooseCarton($order_no)
    {
        $delete_loosecarton = new DeleteLooseCarton();
        $loosecarton_query = $delete_loosecarton->query()->getSql();
        return DB::delete($loosecarton_query, [':order_no' => $order_no]);
    }
}
