<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class OrderDetail implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT  ordhead.order_No , item_master.item_parent, ordsku.item, ordloc.location, 
        	ordloc.loc_type, ordloc.qty_Ordered, ordsku.origin_country_id,
            ordloc.unit_retail, item_master.pack_ind, item_master.simple_pack_ind,item_master.item_desc as description
          	from ordhead
          	inner join ordloc on ordhead.order_no = ordloc.order_no 
          	inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item
          	inner join item_master on item_master.item = ordloc.item
          	where ordhead.order_no = :order_no";

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
