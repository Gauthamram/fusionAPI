<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use Setting;

class Carton extends Model {

	public function getCartonSequence()
	{
    	return $this->cartonsequence = Setting::get('cartonsequence');
	}

	/**
	 * store the sequence number in the database
	 */
	public function setCartonSequence()
	{
		Setting::set('cartonsequence',$this->cartonsequence);
        Setting::save();
	}

	/**
	 * add carton barcode and number array 
	 * @param array array of carton number and barcode
	 */
	public function addCarton(array $details)
	{
		$this->cartondetails = $details;
	}

	/**
	 * set carton barcode and number
	 */
	public function setCartonDetails()
	{
		$quantity = $this->CalculateQuantityPerCarton();
		
		for ($i=1; $i <= $quantity; $i++) {
			$barcode = $this->generateCartonBarcodeNumber();
			$details[] = $barcode;
		}
		
		$this->addCarton($details);

		return $this;
	}

	/**
	 * Returns an array with generated barcode and number for carton 
	 * @return array   
	 */
	public function generateCartonBarcodeNumber()
	{
		$carton_sequence = $this->cartonsequence + 1;
      
		if($carton_sequence == 999999999){
			$carton_sequence = 1;
		} 

    	$this->cartonsequence = $carton_sequence;

		$cartonsequence = str_pad($carton_sequence,9,"0",STR_PAD_LEFT);
		$cs_no_check = config::get('ticket.ssccompanyprefix').$cartonsequence;
		$cs_check = $this->luhn_check_digit($cs_no_check);
		$cs_barcode = $cs_no_check . $cs_check;
		$cartonBarcodes = [
		  'barcode' => $cs_barcode, 
		  'number' => '('.substr($cs_barcode, 0,2).') '.substr($cs_barcode, 2)
		];

		return $cartonBarcodes;
	}

	/**
	 * set product indicator barcode and number
	 */
	public function setProductIndicator()
	{
		$productIndicator = $this->GenerateProductBarcode();

		$this->productIndicatorNumber = $productIndicator['number'];
		$this->productIndicatorBarcode = $productIndicator['barcode'];

		return $this;
	}

	/**
	 * Returns an array with product barcode and number
	 * @param int order number 
	 * @param int item number    
	 * @param int order quantity 
	 * @return  array 
	 */
	public function GenerateProductBarcode()
    {
      $number = config::get('ticket.productindicator.first')." ".$this->order_number." ".config::get('ticket.productindicator.second')." ".$this->item." ".config::get('ticket.productindicator.third')." ".$this->quantity;
      
      $productindicator = [
        'number' => $number,
        'barcode' => str_replace(array(')','(',' '),"",$number),
      ];

      return $productindicator;
    }

    /**
     * [CalcQtyPerCarton check to create number of required cartons based on aty and pick loc qty]
     * @param [int] $qty    [qty of the items]
     * @param [type] $qtyloc [description]
     */
    public function CalculateQuantityPerCarton()
    {
      /**
       * for loose and mixed carton details
       */
      if ($this->pick_location) {
        return ceil(($this->quantity/$this->pick_location));
      } else {
        return $this->quantity;
      }
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
}