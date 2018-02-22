<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Config;

class Sticky extends Model
{

    /**
     * GetDataByStickyType details of the data required by item to create sticky details
     *
     */
    public function setStickyData()
    {
        //default  set as values for not simple pack
        $simplepack = false;
        $pricing = false;

        //set simplepack flag based on item type
        if ($this->type == config::get('ticket.simplepack')) {
            $simplepack = true;
            $this->barcode = $this->packbarcode;
            $this->stockroomlocator = "PACK ".$this->pack_quantity;
        }

        //Quantity calculation based on simplepack flag
        if ($simplepack) {
            $return_value = $this->quantity;
        } else {
            $return_value = ($this->pack_quantity * $this->quantity * (int) $this->GetDivisionMultiplier());

            // if (strtolower($this->div_name) == 'footwear' || strtolower($this->div_name) == 'accessories') {
            //     $pricing = true;
            // }
        }

        //pricing aud
        if (($pricing) and ($this->aud > 0)) {
            $this->aud = $this->aud;
        }

        //pricing nzd
        if ($pricing and $this->nzd > 0) {
            if ($this->channel_id == 23 || $this->channel_id ==25) {
                $this->nzd = $this->nzd;
            }
        }

        $this->printquantity = ceil((int) $return_value * (100 + (int) config::get('ticket.overprintpercentage'))/100);
    }

    /**
     * GetDivision of the order
     */
    public function getDivisionMultiplier()
    {
        switch (strtolower($this->division)) {
            case 'accessories':
                return 2;
            break;

            case 'accessories':
                if ($this->type == config::set('ticket.looseitem')) {
                    return 2;
                } else {
                    return 1;
                }
            break;

            case 'footwear':
                return 1;
            break;
                
            case 'apparel':
                if ($this->channel == 23 || $this->channel == 25) {
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
}
