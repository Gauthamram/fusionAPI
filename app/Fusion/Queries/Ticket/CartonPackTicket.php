<?php

namespace App\Fusion\Queries\Ticket;

use App\Fusion\Contracts\RawSqlInterface;

class CartonPackTicket implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "select ticket_request.Create_DateTime, ticket_request.Ticket_Type_ID, 
        	ticket_request.Order_No as order_number, ticket_request.Printer_Type, 
          	PackStyle.Style, ordsku.pickup_no as pack_type, ticket_request.item, 
          	ticket_request.QTY quantity, ordloc.qty_ordered,
          	ordsku.PickUP_LOC as pick_location, 
      		item_master.item_desc as description, Groups.Group_Name, Deps.Dept_Name as department_name, 
      		Class.Class_Name, SubClass.Sub_Name as sub_class_name, ordloc.loc_type as location_type, ordloc.location 
    	  	from ticket_request 
			inner join ordloc on ticket_request.order_no = ordloc.order_no and ordloc.item = ticket_request.item 
			and ordloc.location = ticket_request.location
			inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
			inner join item_master on item_master.item = ordloc.item and item_master.pack_ind = 'Y' 
			and item_master.simple_pack_ind <> 'Y'
			inner join subclass on item_master.subclass = subclass.subclass and item_master.class = subclass.class 
			and item_master.dept = subclass.dept 
			inner join class on item_master.class = class.class and item_master.dept = class.dept 
			inner join deps on item_master.dept = deps.dept 
			inner join groups on deps.group_no = groups.group_no 
			inner join ( select pack_no, max(item_parent) as Style from packitem group by pack_no ) PackStyle on 	
			ticket_request.item = PackStyle.pack_no 
          	where ticket_request.ticket_type_ID = 'CTRN' and print_online_ind = 'Y' AND ticket_request.order_no = :order_no and ticket_request.item = :item_number
          	Order by  ticket_request.Order_No , item_master.Pack_Type, ticket_request.item";

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
