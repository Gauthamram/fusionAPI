<?php

namespace App\Fusion\Queries\Ticket;

use App\Fusion\Contracts\RawSqlInterface;

class CartonLooseTicket implements RawSqlInterface
{
    public function query()
    {
        $this->sql = " select ticket_request.Create_DateTime, ticket_request.Order_No as order_number, 
        		ticket_request.Ticket_Type_ID,   ticket_request.Printer_Type, 
              	ticket_request.item, item_master.item_parent as style, sizeDiff.Diff_Desc as item_size, 
              	colour.diff_desc  as Colour, ticket_request.QTY as overprint_quantity, 
              	ordloc.QTY_Ordered as quantity, ordsku.PickUP_LOC as pick_location, 
              	item_master.item_desc as description, ordhead.pickup_date, ordhead.Supplier 
				from ticket_request 
				inner join ordhead on ticket_request.order_no = ordhead.order_no 
				inner join ordloc on ticket_request.order_no = ordloc.order_no 
				and ordloc.item = ticket_request.item and ordloc.location = ticket_request.location 
				inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
				inner join item_master on item_master.item = ordloc.item and  item_master.pack_ind = 'N' 
				left join diff_ids colour on item_master.diff_1 = colour.diff_id and colour.diff_type = 'C' 
				left join diff_ids sizeDiff on item_master.diff_2 = sizeDiff.diff_id and sizeDiff.diff_type = 'S' 
				where ticket_request.ticket_type_ID = 'CTRN' 
  				AND ( ticket_request.Order_No = :order_no OR :order_no is null ) 
                UNION 
				select ticket_request.Create_DateTime, ticket_request.Order_No , ticket_request.Ticket_Type_ID,   
				ticket_request.Printer_Type, ticket_request.item, item.item_parent as style, 
				sizeDiff.Diff_Desc as item_size, colour.diff_desc  as Colour, ticket_request.QTY as PrintQTY, 
				ordloc.QTY_Ordered, ordsku.PickUP_LOC , 
				pack.item_desc , ordhead.pickup_date, ordhead.Supplier 
				from ticket_request 
				inner join ordhead on ticket_request.order_no = ordhead.order_no 
				inner join ordloc on ticket_request.order_no = ordloc.order_no and ordloc.item = ticket_request.item 
				and ordloc.location = ticket_request.location 
				inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
				inner join item_master pack on pack.item = ordloc.item and  pack.pack_ind = 'Y' 
				and pack.simple_pack_ind = 'Y' 
				inner join packitem on ordloc.item = packitem.pack_no 
				inner join item_master item on packitem.item = item.item 
				left join diff_ids colour on item.diff_1 = colour.diff_id and colour.diff_type = 'C' 
				left join diff_ids sizeDiff on item.diff_2 = sizeDiff.diff_id and sizeDiff.diff_type = 'S' 
				where ticket_request.ticket_type_ID = 'CTRN' 
			  	AND ( ticket_request.Order_No = :order_no OR :order_no is null AND ticket_request.item = :item_number)";

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
