<?php

namespace App\Fusion\Queries\Ticket;

use App\Fusion\Contracts\RawSqlInterface;

class DeleteLooseCarton implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "delete  ticket_request 
              	where ticket_request.ticket_type_id = 'CTRN' and ticket_request.order_no in 
              	( select  distinct ordhead.Order_No 
                from ticket_request 
             	inner join ordloc on  ordloc.item = ticket_request.item and ordloc.location = ticket_request.location 
             	and ticket_request.ticket_type_id = 'CTRN' 
             	inner  join ordhead on Ordloc.Order_No = ordhead.order_no 
             	inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
         		inner join item_master on item_master.item = ordloc.item 
         		and ( item_master.pack_ind = 'N' or item_master.simple_pack_ind = 'Y' ) 
                where ordsku.PickUP_LOC is null and ordloc.QTY_Ordered > 0 and ticket_request.order_no = :order_no)";

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
