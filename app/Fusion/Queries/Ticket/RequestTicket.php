<?php

namespace App\Fusion\Queries\Ticket;

use App\Fusion\Contracts\RawSqlInterface;

class RequestTicket implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT  tr.Order_No, tr.Ticket_Type_ID, tr.Sort_Order_Type, tr.Printer_Type, 
        	tr.Qty As Quantity, 
          	tr.Location, im.item as item_number, im.pack_ind, im.simple_pack_ind, diff_1, diff_2, pack_type as packtype
          	FROM ticket_request tr
          	INNER JOIN item_master im ON tr.item = im.item
          	WHERE tr.item = :item_number AND tr.order_no = :order_no and print_online_ind = 'Y'
          	ORDER BY tr.Order_No, tr.Ticket_Type_ID, tr.Sort_Order_Type, tr.Printer_Type";

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
