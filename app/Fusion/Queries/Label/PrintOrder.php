<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class PrintOrder implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT distinct ordhead.order_no as order_number, sups.sup_name as supplier_name,
        	datetime(ordhead.ORIG_APPROVAL_DATE, 'unixepoch', 'localtime') as approved_date, ordhead.status
			from ordhead
			inner join ordloc on ordhead.order_no = ordloc.order_no
			inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item
			inner join item_master on item_master.item = ordloc.item
			inner join deps on item_master.dept = deps.dept
			inner join groups on deps.group_no = groups.group_no
			inner join sup_traits_matrix on ordhead.supplier = sup_traits_matrix.supplier
			and sup_traits_matrix.sup_trait = :supplier_trait
			inner join sups on sups.supplier = ordhead.supplier
            where (ordhead.status = 'A' or ordhead.status = 'C')";

        return $this;
    }

    public function forAdmin() {
         // $this->sql .= " )";

        return $this;
    }

    public function forSupplier()
    {
        $this->sql .= " and ordhead.supplier = :supplier";

        return $this;
    }

    public function filter($param = '')
    {

    }

    public function getSql()
    {
        $this->sql .= " order by approved_date desc";

        return $this->sql;
    }
}
