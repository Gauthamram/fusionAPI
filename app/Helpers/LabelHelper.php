<?php 

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Config;
use App\Helpers\SqlHelper;
use Cache;
use Carbon\Carbon;
use App\Fusion\UserSetting;
use Illuminate\Support\Facades\Log;
use App\TipsTicketPrinted;

class LabelHelper extends ApiHelper
{
    /**
     * [$setting_name - restrict retrieval of only named settings instead of everything]
     * @var string
     */
    protected $setting_name = 'label%';
    protected $admin = false;
    
    /**
     * [__construct]
     */
    public function __construct(userSetting $userSetting)
    {
      $this->admin = $userSetting->isAdmin();
      $this->warehouse = $userSetting->isWarehouse();
      $this->sqlHelper = New sqlHelper($userSetting);
      $this->setting = $this->setSetting($this->setting_name);
      $this->userSetting = $userSetting->getuserSetting(); 
    }
       
	  /**
     * [supplier details of the supplier order]
     * @param [int] $supplier [supplierid]
     */
    public function OrderSupplier($supplierid,$type)
    {
      $supplier = Cache::remember("'".$this->userSetting['number']."-ordersupplier",Carbon::now()->addMinutes(60),function() use ($type) {
        $supplier_query = $this->sqlHelper->GetSql('Supplier','',$type);
        if($this->admin || $this->warehouse){
          $supplier = DB::select($supplier_query);
        } else {
          $supplier = DB::select($supplier_query,[':supplier'=>$this->userSetting['number']]);
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
        $cartonpack_query = $this->sqlHelper->GetSql('CartonPack','',$item_number);
        
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
      $cartonloose_query = $this->sqlHelper->GetSql('CartonLoose','',$item_number);
        
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
      $stickies_query = $this->sqlHelper->GetSql($type,'','');

      $stickies = DB::select($stickies_query,[':order_no'=>$order_no]);

      $stickydetails = array_map([$this,"StickyDetails"], $stickies);
      
      return $stickydetails;
    }

    /**
     * [OrderTipsTicketData create tips ticket for the order]
     * @param [type] $order_no    [number of the order]
     * @param [type] $item_number [item number]
     */
    public function TipsTicketData($order_no, $item_number)
    {
      //get ticket request 
      $ticketrequest_query = $this->sqlHelper->GetSql('ticketrequests','','');
      $ticketrequests = DB::select($ticketrequest_query,[':order_no' => $order_no,':item_number' => $item_number]);

      
      //delete all bad requests for the loosecartons
      $deleteloosecarton = $this->DeleteLooseCarton();

      foreach ($ticketrequests as $ticketrequest) {
        if ($ticketrequest->ticket_type_id == config::get('ticket.type.carton')) {
           $data['carton']['pack'] = $cartonpackdata = $this->TicketRequestCartonPack($ticketrequest);
           $data['carton']['loose'] = $cartonloosedata = $this->TicketRequestCartonPack($ticketrequest);

           //get quantity
           $quantity['Carton_Pack'] = $quantity['Carton_Pack'] + $cartonpackdata['quantity'];
           $quantity['Carton_Loose'] = $quantity['Carton_Loose'] + $cartonloosedata['quantity'];
        } else {
          $thePackIndicator = $this->CheckPackIndicator($ticketrequest); 
          
          switch ($thePackIndicator) {
            case 'none':
              $data['sticky'][] = $stickydata = $this->TicketRequestItem($ticketrequest);
              break;
            case 'simple':
              $data['sticky'][] = $stickydata = $this->TicketRequestSimplePack($ticketrequest);
              break;
            case 'transport':
              $data['sticky'][] = $stickydata = $this->TicketRequestPack($ticketrequest);  
              break;    
            default:
              $data['sticky'];
              break;
          }
          $quantity['Sticky_Label'] = $quantity['Sticky_Label'] + $stickydata['quantity'];
        }
      }

      //set ticket printed
      foreach ($quantity as $name => $labelquantity) {
         $ticketprinted = New TipsTicketPrinted();
         $ticketprinted->order_no = $order_no;
         $ticketprinted->createdate = Carbon::now();
         $ticketprinted->filename = Config::get('ticket.filename');
         $ticketprinted->tickettype = $name;
         $ticketprinted->save();
       } 

      return $data;  
    }

    /**
     * [TicketRequestCartonLoose - Cartonloose data]
     * @param [type] $ticket [ticketrequest]
     */
    public function TicketRequestCartonLoose($ticket)
    {
      //get item
      $cartonloose_query = $this->sqlHelper->GetSql('ticketcartonpack','','');
      $cartonloose = DB::select($cartonloose_query,[':order_no' => $ticket->order_no]);
      
      $cartonloosedetails = array_map([$this,"CartonDetails"], $cartonloose); 

      return $cartonloosedetails;   
    }

    /**
     * [TicketRequestCartonPack Cartonpack data]
     * @param [type] $ticket [ticketrequest]
     */
    public function TicketRequestCartonPack($ticket)
    {
      //get item
      $cartonpack_query = $this->sqlHelper->GetSql('ticketcartonpack','','');
      $cartonpacks = DB::select($cartonpack_query,[':order_no' => $ticket->order_no]);
      
      $cartonpackdetails = array_map([$this,"CartonDetails"], $cartonpacks); 

      return $cartonpackdetails;     
    }

    /**
     * [TicketRequestItem Item data]
     * @param [type] $ticket [ticketrequest]
     */
    public function TicketRequestItem($ticket)
    {
      //get item
      $orderitem_query = $this->sqlHelper->GetSql('ticketitem','','');
      $orderitems = DB::select($orderitem_query,[':order_no' => $ticket->order_no,':item_number' => $ticket->item_number]);

      foreach ($orderitems as $orderitem) {
        if ($prev_item != $orderitem->itemnumber) {
          $prev_item = $orderitem->itemnumber;

          $itemdata = array(
            'product_number' => $orderitem->itemnumber,
            'stock' => $orderitem->stockroom,
            'product_name' => $orderitem->short_desc.' : '.$orderitem->colour.' : '.$orderitem->size,
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
    public function TicketRequestSimplePack($ticket)
    {
      $prev_item = '';
      $i =1;
      //get simple pack items
      $ordersimplepack_query = $this->sqlHelper->GetSql('ticketsimplepack','','');
      $ordersimplepacks = DB::select($ordersimplepack_query,[':order_no' => $ticket->order_no,':packnumber' => $ticket->itemnumber,':location1' => $ticket->location,':location2' => $ticket->location,':location3' => $ticket->location]);

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
          if ($prev_item != $ordersimplepack->itemnumber) {
            $prev_item = $ordersimplepack->itemnumber;

            $simplepackdata[$i][$key] = array(
              'product_number' => $orderpack->itemnumber,
              'stock' => $orderpack->stockroom,
              'product_name' => $orderpack->short_desc.' : '.$orderpack->colour.' : '.$orderpack->item_size,
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
    public function TicketRequestPack($ticket)
    {
      $prev_item = '';
      $i =1;
      //get pack items
      $orderpack_query = $this->sqlHelper->GetSql('ticketpack','','');
      $orderpacks = DB::select($orderpack_query,[':order_no' => $ticket->order_no,':packnumber' => $ticket->itemnumber,':location1' => $ticket->location,
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
          if ($prev_item != $orderpack->itemnumber) {
            $prev_item = $orderpack->itemnumber;

            $packdata[$i][$key] = array(
              'product_number' => $orderpack->itemnumber,
              'stock' => $orderpack->stockroom,
              'product_name' => $orderpack->short_desc.' : '.$orderpack->colour.' : '.$orderpack->item_size,
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
    public function DeleteLooseCarton($ticket)
    {
        $loosecarton_query = $this->sqlHelper->GetSql('deleteloosecartons','','');
        return DB::select($cartonloose_query,[':order_no' => $ticket->order_no]);
    } 
}