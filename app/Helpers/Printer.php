<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Config;
use App\Order;
use App\Carton;
use App\Supplier;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Fusion\Commands\Sql;
use JWTAuth;

class Printer 
{

  protected $admin = false;
  
  public function __construct() 
  {
    $this->user = JWTAuth::parseToken()->authenticate();
    $this->sql = New sql($this->user);
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

	/**
	 * [CheckPackIndicator - pack indicator check and return value]
	 * @param [type] $ticket [ticket request object]
	 */
	public function CheckPackIndicator($ticket)
    {
      $packindicator = 'none';

      if ($ticket->pack_ind == 'Y') {
        $packindicator = 'transport'; config::get('ticket.packindicator.transport');
        if($ticket->simple_pack_ind == 'Y'){
          $packindicator = 'simple'; config::get('ticket.packindicator.simple');
        }
      }

      return $packindicator; 
    }

    /**
     * [CartonDetails]
     * @param [object] $carton [carton object from the collections]
     */
    public function CartonDetails($cartons)
    {
      $cartons = Carton::hydrate($cartons);
      
      foreach ($cartons as $carton) {
          $carton->getCartonSequence();
          $carton->setCartonDetails();
          $carton->setProductIndicator();
          $cartondetails[] = $carton;
      }
      
      $carton->setCartonSequence();
      return $cartondetails;  
    }

    /**
     * [StickyDetails]
     * @param [object] $sticky [object from query result]
     */
    public function StickyDetails($sticky)
    {
      
      $detail = array(
        'itemnumber'    => $sticky->item,
        'description1'  => $sticky->description,
        'description2'  => $sticky->colour." ".$sticky->size,
        'size'          => $sticky->size,
        'stockroomlocator' => $sticky->stockroomlocator,
        'manufacturedate' => $sticky->earliest_ship_date,
        'barcode'       => $sticky->barcode,
        'barcodetype' => $sticky->barcodetype 
      );

      $details = array_merge($detail,$this->GetDataByStickyType($sticky));

      return $details;
    }

    /**
     * [checkEDI description]
     * @param  [int] $order_no [order number]
     * @return [boolean]           [edi-check yes/no]
     */
    public function EDICheck($order_no)
    {
      if ($cache_value = Cache::get("'".$order_no."-isEDI", false)){
        return $cache_value;  
      } else {
        $order = Order::find($order_no);
      } 
      if($order->edi_po_ind == config::get('ticket.edi')){
          Cache::put("'".$order_no."-isEDI",true,60);
          return true;
      } else {
          Cache::put("'".$order_no."-isEDI",false,60);
          return false;
      }
    }

    /**
     * [GetDivision of the order]
     * @param [string] $division [category of the item]
     * @param [string] $type     [type of the item]
     * @param [int] $channel  [channel of the item]
     */
    public function GetDivisionMultiplier($division,$type,$channel)
    {
      switch ($division) {
        case 'accessories':
           return 2;
          break;
        
        case 'accessories':
          if($type == $this->setting('looseitem')){
           return 2;
          } else {
           return 1; 
          }
          break;
        
        case 'footwear':
           return 1;
          break;
            
        case 'apparel':
          if($channel == 23 || $channel == 25){
           return 2;
          } else {
           return 1; 
          }
          break;

        default:
          return 1;
          break;
      }
    }

    /**
     * [GetDataByStickyType details of the data required by item to create sticky details]
     * @param [object] $item [item object from the parent]
     */
    public function GetDataByStickyType($item)
    {
      //default  set as values for not simple pack
      $simplepack = false;
      $pricing = false;
      
      //simple pack flag set
      if($item->type == $this->setting('looseitem')){
        $simplepack = true;
        $stickydata = array(
          'barcode' => $sticky->packbarcode,
          'stockroomlocator' => "PACK ".$sticky->pack_qty
          );
      } 

      //Quantity calculation based on simplepack flag
      if ($simplepack) {
        $return_value = $orderqty;
      } else {
        $return_value = ($packqty * $orderqty * (int) $this->GetDivisionMultiplier($item->div_name,$item->type,$item->channel_id)); 
        
        if (strtolower($item->div_name) == 'footwear' || strtolower($item->div_name) == 'accessories') {
          $pricing = true;
        } 
      }

      //pricing aud
      if (($pricing) and ($sticky->aud > 0)) {
        $stickydata['aud'] = $sticky->aud;
      } 

      //pricing nzd
      if ($pricing and $sticky->nzd > 0) {
        if ($sticky->channel_id == 23 || $sticky->channel_id ==25) {
          $stickydata['nzd'] = $sticky->nzd;
        }
      }

      $stickydata['printquantity'] = ceil((int) $return_value * (100 + (int) $this->setting('overprintpercentage'))/100);

      return $stickydata;
    }
}