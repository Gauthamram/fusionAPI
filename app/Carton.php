<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;
use Setting;

class Carton extends Model
{
    public function getCartonSequence()
    {
        return $this->carton_sequence = Setting::get('cartonsequence');
    }

   

    /**
     * store the sequence number in the database
     */
    public function setCartonSequence()
    {
        Setting::set('cartonsequence', $this->carton_sequence);
        Setting::save();
    }

    /**
     * set carton barcode and number
     */
    public function setBarcodeNumberDetails()
    {
        $quantity = $this->calculateQuantityPerCarton();
        
        for ($i=1; $i <= $quantity; $i++) {
            $this->getCartonSequence();

            $quantity_check = ($this->quantity - ($i * $this->pick_location));
            if(($quantity_check >= $this->pick_location) || ($quantity_check == 0)) {
                $this->piquantity = $this->pick_location;
            } else {
                $this->piquantity = $quantity_check;
            }

            $barcode = ($this->generateCartonBarcodeNumber() + $this->generateProductBarcode());
            $details[] = $barcode;
        }
        
        $this->addCarton($details);

        return $this;
    }

    /**
     * add carton barcode and number array
     * @param array array of carton number and barcode
     */
    public function addCarton(array $details)
    {
        $this->carton_details = $details;
    }

    /**
     * Returns an array with generated barcode and number for carton
     * @return array
     */
    public function generateCartonBarcodeNumber()
    {
        $sequence = $this->carton_sequence + 1;
      
        if ($sequence == 999999999) {
            $sequence = 1;
        }

        $this->carton_sequence = $sequence;

        $sequence = str_pad($sequence, 9, "0", STR_PAD_LEFT);
        $cs_no_check = config::get('ticket.ssccompanyprefix').$sequence;
        $cs_check = $this->luhn_check_digit($cs_no_check);
        $cs_barcode = $cs_no_check . $cs_check;
        $carton_barcodes = [
          'barcode' => $cs_barcode,
          'number' => '('.substr($cs_barcode, 0, 2).') '.substr($cs_barcode, 2)
        ];

        $this->setCartonSequence();

        return $carton_barcodes;
    }


    /**
     * set product indicator barcode and number
     */
    public function setProductIndicator()
    {
        $indicator = $this->generateProductBarcode();

        $this->product_indicator_number = $indicator['number'];
        $this->product_indicator_barcode = $indicator['barcode'];

        return $this;
    }

    /**
     * Returns an array with product barcode and number
     * @param int order number
     * @param int item number
     * @param int order quantity
     * @return  array
     */
    public function generateProductBarcode()
    {
        $number = config::get('ticket.productindicator.first')." ".$this->order_number." ".config::get('ticket.productindicator.second')." ".$this->item." ".config::get('ticket.productindicator.third')." ".$this->piquantity;
      
        $productindicator = [
        'pinumber' => $number,
        'pibarcode' => str_replace(array(')','(',' '), "", $number),
      ];

        return $productindicator;
    }

    /**
     * [CalcQtyPerCarton check to create number of required cartons based on aty and pick loc qty]
     * @param [int] $qty    [qty of the items]
     * @param [type] $qtyloc [description]
     */
    public function calculateQuantityPerCarton()
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
