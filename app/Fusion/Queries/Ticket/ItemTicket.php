<?php

namespace App\Fusion\Queries\Ticket;

use App\Fusion\Contracts\RawSqlInterface;

class ItemTicket implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT  im.item_parent AS productnumber, im.item as item_number, im.short_desc,
        	sz.diff_desc AS item_size, cl.diff_desc AS colour,
            1 AS quantity, srl.uda_text as stockroomlocator, v.uda_value_desc  AS brand, os.earliest_ship_date,
            br.item AS 	barcode, 99999 AS sortid,
            99999 AS sortnumid, NVL(os.pickup_no, 'ZZZ') AS packtype,
            CASE WHEN EXISTS (SELECT wh FROM wh WHERE wh = :location1)
            THEN (SELECT NVL(i.unit_retail, 0) AS unit_retail
            FROM   item_zone_price i, price_zone p
            WHERE  p.zone_id = i.zone_id
            AND    im.item = i.item
            AND    p.zone_group_id = im.retail_zone_group_id
            AND    i.zone_id = (SELECT z.zone_id
            FROM store s
            INNER JOIN price_zone_group_store z ON s.store = z.store AND s.country_id  = 'AU'
            WHERE  s.default_wh = :location2
            AND ROWID = 1))
            ELSE (SELECT NVL(i.unit_retail, 0) AS unit_retail
            FROM   item_zone_price i
            INNER JOIN price_zone p ON p.zone_id = i.zone_id
            INNER JOIN price_zone_group_store z ON i.zone_id = z.zone_id
            WHERE  im.item = i.item
            AND    p.zone_group_id = im.retail_zone_group_id
            AND    z.store = :location3)
            END AS auprice,
            NVL(nz.unit_retail, 0) AS nzprice
            FROM  item_master im
            INNER JOIN ordsku os ON os.item = im.item
            LEFT JOIN diff_ids sz ON im.diff_2 = sz.diff_id and sz.diff_type = 'S'
            LEFT JOIN diff_ids cl ON im.diff_1 = cl.diff_id and cl.diff_type = 'C'
            LEFT JOIN uda_item_ff srl ON srl.item = im.item and srl.uda_id = 900
            LEFT JOIN uda_item_lov uil ON uil.item = im.item
            INNER JOIN uda_values v ON uil.uda_id = v.uda_id AND uil.uda_value = v.uda_value AND v.uda_id = 8
            LEFT JOIN item_master br ON br.item_parent = im.item AND br.primary_ref_item_ind = 'Y'
            LEFT JOIN item_zone_price nz ON nz.item = im.item and nz.zone_id = 4
            WHERE os.order_no = :order_no AND im.item = :item_number";

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
