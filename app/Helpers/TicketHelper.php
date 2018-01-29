<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Config;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\TipsTicketPrinted;
use App\TicketRequest;
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
        $data = array();

        foreach ($ticketrequests as $ticketrequest) {
            if ($ticketrequest->ticket_type_id == config::get('ticket.type.carton')) {
                
                if($cartonpackdata = $this->ticketRequestCartonPack($ticketrequest)) {
                  $data['cartonpack'] = $cartonpackdata;
                } 

                if($cartonloosedata = $this->ticketRequestCartonLoose($ticketrequest)){
                  $data['cartonloose'] = $cartonloosedata;
                };
            } else {
                $thePackIndicator = $this->checkPackIndicator($ticketrequest);
          
                switch ($thePackIndicator) {
                    case 'none':
                      if($stickydata = $this->ticketRequestItem($ticketrequest)) {
                        $data['sticky'] = $stickydata;  
                      }
                      break;
                    case 'simple':
                      if($stickydata = $this->ticketRequestSimplePack($ticketrequest)) {
                        $data['simplepack'] = $stickydata;  
                      }
                      break;
                    case 'transport':
                       if($stickydata = $this->ticketRequestPack($ticketrequest)) {
                        $data['pack'][] = $stickydata;  
                      }
                      break;
                    default:
                      $data['sticky'];
                      break;
                }
            }
            $delete_ticket_request = $this->deleteTicketRequest($ticketrequest);
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
        $cartonloose = DB::select($cartonloose_query, [':order_no' => $ticket->order_no, ':item_number' => $ticket->item_number]);
      
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
        $cartonpacks = DB::select($cartonpack_query, [':order_no' => $ticket->order_no, ':item_number' => $ticket->item_number]);
      
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
        if($orderitems) {
          foreach ($orderitems as $orderitem) {
            if ($prev_item != $orderitem->item_number) {
                $prev_item = $orderitem->item_number;

                $itemdata[] = array(
                    'order_number' => $ticket->order_no,
                    'item' => $orderitem->item_number,
                    'stockroomlocator' => $orderitem->stockroomlocator,
                    'description' => $orderitem->short_desc,
                    'colour' => $orderitem->colour,
                    'item_size' => $orderitem->item_size,
                    'quantity' => $ticket->quantity,
                    'barcode' => $orderitem->barcode
                  );
            }
          }
        } else {
          $itemdata = [];
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

        //get simple pack items
        $ordersimplepack_query = $simplepack_ticket->query()->getSql();
        $ordersimplepacks = DB::select($ordersimplepack_query, [':order_no' => $ticket->order_no,':packnumber' => $ticket->item_number,':location1' => $ticket->location,':location2' => $ticket->location,':location3' => $ticket->location]);

        if($ordersimplepacks) { 
          $simplepackdata = array(
            'order_number' => $ticket->order_no,
            'quantity' => $ticket->quantity,
            'pack_number' => $ticket->item_number
          );
          foreach ($ordersimplepacks as $key => $ordersimplepack) {
              if ($prev_item != $ordersimplepack->item_number) {
                  $prev_item = $ordersimplepack->item_number;

                  $data[$ordersimplepack->item_number] = array(
                    'stockroomlocator' => $orderpack->stockroomlocator,
                    'description' => $orderpack->short_desc,
                    'colour' => $orderpack->colour,
                    'item_size' => $orderpack->item_size,
                    'quantity' => $orderpack->quantity,
                    'barcode' => $orderpack->barcode
                  );
              }
          }

          $simplepackdata['pack_type'] = $ordersimplepack->packtype;
          $simplepackdata['packs'] = $data;
        } else {
          $simplepackdata = [];
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

        $data = array();
        $pack_ticket = new PackTicket();

        //get pack items
        $orderpack_query = $pack_ticket->query()->getSql();
        $orderpacks = DB::select($orderpack_query, [':order_no' => $ticket->order_no,':packnumber' => $ticket->item_number,':location1' => $ticket->location,
        ':location2' => $ticket->location,':location3' => $ticket->location]);

        if($orderpacks) { 
          $packdata = array(
            'order_number' => $ticket->order_no,
            'quantity' => $ticket->quantity,
            'pack_number' => $ticket->item_number  
          );

          foreach ($orderpacks as $key => $orderpack) {
              if ($prev_item != $orderpack->item_number) {
                  $prev_item = $orderpack->item_number;

                  $data[$orderpack->item_number] = array(
                    'stockroomlocator' => $orderpack->stockroomlocator,
                    'description' => $orderpack->short_desc,
                    'colour' => $orderpack->colour,
                    'item_size' => $orderpack->item_size,
                    'quantity' => $orderpack->quantity,
                    'barcode' => $orderpack->barcode
                  );
              }
          }

          $packdata['pack_type'] = $orderpack->packtype;
          $packdata['packs'] = $data;
        } else {
          $packdata = [];
        }
        return $packdata;
    }

    /**
     * [DeleteTicketRequest - Delete request after processing]
     * @param [type] $ticket [ticketrequest object]
     */
    public function deleteTicketRequest($ticket)
    {
        $deletion = TicketRequest::where('item', $ticket->item_number)
                    ->Where('order_no', $ticket->order_no)
                    ->delete();

        return $deletion;
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
