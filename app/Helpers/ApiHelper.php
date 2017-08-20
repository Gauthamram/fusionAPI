<?php 

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Config;
use Setting;
use App\Order;
use App\Supplier;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Fusion\Traits\UserSettingTrait;
use App\Fusion\Interfaces\iUserSetting;
use App\Fusion\Commands\Sql;

class ApiHelper implements iUserSetting
{
  use UserSettingTrait;

  protected $setting;
  protected $admin = false;
  
  public function __construct() 
  {
    $this->sql = New sql();
    $this->setting = $this->setSetting($this->setting_name);
    $this->setUserSetting();
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
	 * [setSetting for the requested setting to be retrieved by name]
	 * @param [type] $name [type of setting]
	 */
	public function setSetting($name)
	{
		Setting::setConstraint(function($query,$insert = false) use ($name){
        	$query->Where('keys','like',$name);
      	});

		$process_setting = Cache::remember('settings', Carbon::now()->addMinutes(60), function() {
		return Setting::all();
		});

		return $this->setting = $process_setting['label'];
	}

  public function getSetting() 
  {
      return $this->setting;
  }

	public function setCartonSequence()
	{
		//save Config carton sequence number to db
        Setting::set('label.cartonsequence',$this->setting['cartonsequence']);
        Setting::save();
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
    public function CartonDetails($carton)
    {
      $cartondetails = array();
      //product indicator and barcode - they are same but different format
      //it is same for all carton under the cartonpack - only chnage for different item and order and quantity combined
      $productindicator = $this->GenerateProductBarcode($carton->order_no,$carton->item,1);

      $details = array(
        'ordernumber' => $carton->order_no,
        'printquantity' => '1', 
        'style' => $carton->style,
        'description' => $carton->item_desc,
        'productindicatorbarcode' => $productindicator['barcode'],
        'productindicator' => $productindicator['number']
      );  
  
      if(!(property_exists($carton, "pickup_no"))){
        $details['size'] = $carton->diff_desc;
        $details['colour'] = $carton->colour;
        $details['cartonquantity'] = $carton->pickup_loc;
        $details['itemnumber'] = $carton->item;
      } else {
        $details['packnumber'] = $carton->item;
        $details['packtype'] = $carton->pickup_no;
        $details['group'] = $carton->group_name;
        $details['dept'] = $carton->dept_name;
        $details['class'] = $carton->class_name;
        $details['subclass'] = $carton->sub_name;
      }
      /**
       * calculation of aty based on pack or loose carton or simple and mixed 
       */
      $qty = $this->CalcQtyPerCarton($carton->qty_ordered,$carton->pickup_loc);

      //create carton barcode for each pack - if qty is 3 then three barcodes 
      for ($i=1; $i <= $qty; $i++) { 
        $cartonBarcodes = $this->GenerateCartonBarcode();
        //adding carton numbers to the detail array
        $details['carton'][] = $cartonBarcodes;        
      }

      return $details;  
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

      //Try cached items first
      if ($cache_value = Cache::get("'".$order_no."-isEDI", false)){
        return $cache_value;  
      } else {
        $order = Order::find($order_no);
      } 
      if($order->edi_po_ind == $this->setting['edi']){
          Cache::put("'".$order_no."-isEDI",true,60);
          return true;
      } else {
          Cache::put("'".$order_no."-isEDI",false,60);
          return false;
      }
    }

    /**
     * Get product barcode and product barcode indicator generated
     * @param - detail array [0] - order_no,[1] - item,[2] - quantiy
     */
    public function GenerateProductBarcode($orderno,$item,$qty)
    {
      $pinumber = $this->setting['productindicator']['first']." ".$orderno." ".$this->setting['productindicator']['second']." ".$item." ".$this->setting['productindicator']['third']." ".$qty;
      
      $productindicator = [
        'number' => $pinumber,
        'barcode' => str_replace(array(')','(',' '),"",$pinumber),
      ];

      return $productindicator;
    }

    /**
     * Generate Carton barcode for each carton 
     *@params
     */
    public function GenerateCartonBarcode()
    {
      
      $carton_sequence = $this->setting['cartonsequence'] + 1;
      
      if($carton_sequence == 999999999){
        $carton_sequence = 1;
      } 

      $this->setting['cartonsequence'] = $carton_sequence;
      
      $cartonsequence = str_pad($carton_sequence,9,"0",STR_PAD_LEFT);
      $cs_no_check = $this->setting['sscccompanyprefix'].$cartonsequence;
      $cs_check = $this->luhn_check_digit($cs_no_check);
      $cs_barcode = $cs_no_check . $cs_check;
      $cartonBarcodes = [
          'barcode' => $cs_barcode, 
          'number' => '('.substr($cs_barcode, 0,2).') '.substr($cs_barcode, 2)
      ];
    
      return $cartonBarcodes;
    }

    /**
     * [luhn_check_digit to calculate the last digit of the carton]
     * @param  [int] $number [ssccompanyprefix and cartoln sequence]
     * @return [type]         [description]
     */
    public function luhn_check_digit($number)
    {
    
      settype($number, 'string');
      
      $sumTable = array(
        array(0,1,2,3,4,5,6,7,8,9),
        array(0,2,4,6,8,1,3,5,7,9)
      );
      
      $sum = 0;
      $flip = 0;
      
      for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $sum += $sumTable[$flip++ & 0x1][$number[$i]];
      }
      return $sum % 10;
    }

    /**
     * [CalcQtyPerCarton check to create number of required cartons based on aty and pick loc qty]
     * @param [int] $qty    [qty of the items]
     * @param [type] $qtyloc [description]
     */
    public function CalcQtyPerCarton($qty,$qtyloc)
    {
      /**
       * for loose and mixed carton details
       */
      if($qtyloc){
        return ceil(($qty/$qtyloc));
      } else {
        return $qty;
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