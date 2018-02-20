<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class CartonPackOrder implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT ordloc.order_no as order_number, PackStyle.Style, ordsku.pickup_no as pack_type, 
			ordloc.item, ordloc.loc_type as location_type, ordloc.location, ordloc.QTY_Ordered as quantity, ordsku.PickUP_LOC as pick_location,
			item_master.item_desc as description, Groups.Group_Name, Deps.Dept_Name as department_name, 
			Class.Class_Name, SubClass.Sub_Name as sub_class_name, ordhead.EDI_PO_IND as edi_po_index, 
			'CartonPack' as carton_type
			from ordhead
			inner join ordloc on ordhead.order_no = ordloc.order_no 
			inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item
			inner join item_master on item_master.item = ordloc.item and item_master.pack_ind = 'Y' 
			and item_master.simple_pack_ind <> 'Y'
			inner join subclass on item_master.subclass = subclass.subclass and item_master.class = subclass.class 
			and item_master.dept = subclass.dept
			inner join class on item_master.class = class.class and item_master.dept = class.dept
			inner join deps on item_master.dept = deps.dept
			inner join groups on deps.group_no = groups.group_no
			inner join ( select pack_no, max(item_parent) as Style from packitem group by pack_no ) PackStyle 
			on ordloc.item = PackStyle.pack_no
			where ordloc.order_no = :order_no and (ordhead.status = 'A' or ordhead.status = 'C')";

        return $this;
    }

    public function filter($param = '')
    {
        if ($param) {
            $this->sql .= " AND ordloc.item = :item_number";
        }

        return $this;
    }

    public function getSql()
    {
        $this->sql .= " Order by  ordloc.Order_No , ordsku.pickup_no, ordloc.item";

        return $this->sql;
    }
}
