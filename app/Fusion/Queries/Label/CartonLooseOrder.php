<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class CartonLooseOrder implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT  ordhead.order_no as order_number, ordsku.item, item_master.item_parent as style, sizeDiff.Diff_Desc as item_size, colour.diff_desc  as Colour, ordloc.QTY_Ordered as quantity, ordloc.QTY_Ordered, ordsku.PickUP_LOC as pick_location, item_master.item_desc as description, ordhead.pickup_date, ordhead.Supplier, 'CartonLoose' as carton_type, ordhead.EDI_PO_IND as edi_po_index, ordloc.loc_type as location_type, ordloc.location
			from ordhead 
			inner join ordloc on ordhead.order_no = ordloc.order_no
			inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
			inner join item_master on item_master.item = ordloc.item and  item_master.pack_ind = 'N' 
			left join diff_ids colour on item_master.diff_1 = colour.diff_id and colour.diff_type = 'C' 
			left join diff_ids sizeDiff on item_master.diff_2 = sizeDiff.diff_id and sizeDiff.diff_type = 'S' 
			where ordhead.order_no = :order_no  and ordloc.QTY_Ordered > 0 and (ordhead.status = 'A' or ordhead.status = 'C')";

        return $this;
    }

    public function filter($param = '')
    {
        if ($param) {
            $this->sql .= " AND ordloc.item = :item_number";
        }
        
        return $this;
    }

    public function union()
    {
        $this->sql .= " UNION 
			SELECT ordhead.Order_No , ordsku.item, item.item_parent as style, sizeDiff.Diff_Desc, 
			colour.diff_desc  as Colour, ordloc.QTY_Ordered as quantity, ordloc.QTY_Ordered, ordsku.PickUP_LOC , pack.item_desc, ordhead.pickup_date, 
			ordhead.Supplier , 'SimplePack' as CartonType, ordhead.EDI_PO_IND, ordloc.loc_type as location_type, ordloc.location as location
			from ordhead 
			inner join ordloc on ordhead.order_no = ordloc.order_no 
			inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
			inner join item_master pack on pack.item = ordloc.item and  pack.pack_ind = 'Y' and pack.simple_pack_ind = 'Y' 
			inner join packitem on ordloc.item = packitem.pack_no 
			inner join item_master item on packitem.item = item.item 
			left join diff_ids colour on item.diff_1 = colour.diff_id and colour.diff_type = 'C' 
			left join diff_ids sizeDiff on item.diff_2 = sizeDiff.diff_id and sizeDiff.diff_type = 'S' 
			where ordhead.order_no  = :order_no  and ordloc.QTY_Ordered > 0 and (ordhead.status = 'A' or ordhead.status = 'C')";

        return $this;
    }

    public function getSql()
    {
        return $this->sql;
    }
}
