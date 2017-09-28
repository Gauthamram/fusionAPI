<?php

namespace App\Fusion\Queries\Ticket;

use App\Fusion\Contracts\RawSqlInterface;

class SimplePackTicket implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT pi.pack_no AS productnumber, im.item as item_number, im.short_desc, 
        	sz.diff_desc AS item_size, cl.diff_desc AS colour, 
            1 AS quantity, srl.uda_text as stockroom, v.uda_value_desc  AS brand, os.earliest_ship_date, 
            br.item AS barcode, 
            99999 AS sortid, 99999 AS sortnumid, NVL(os.pickup_no, 'ZZZ') AS packtype, 0 as auprice, 0 as nzprice
            FROM item_master im
            INNER JOIN packitem pi ON im.item = pi.item
            INNER JOIN ordsku os ON os.item = pi.pack_no 
            LEFT JOIN diff_ids sz ON im.diff_2 = sz.diff_id and sz.diff_type = 'S'
            LEFT JOIN diff_ids cl ON im.diff_1 = cl.diff_id and cl.diff_type = 'C'
            LEFT JOIN uda_item_ff srl ON srl.item = im.item and srl.uda_id = 900
            LEFT JOIN uda_item_lov uil ON uil.item = im.item
            INNER JOIN uda_values v ON uil.uda_id = v.uda_id AND uil.uda_value = v.uda_value AND v.uda_id = 8
            INNER JOIN item_master br ON br.item_parent = pi.pack_no AND br.primary_ref_item_ind = 'Y'
            WHERE os.order_no = :order_no AND pi.pack_no = :packnumber";

        return $this;
    }

    public function filter($param = '')
    {
        return $this;
    }

    public function getSql()
    {
        return $this->sql;
    }
}
