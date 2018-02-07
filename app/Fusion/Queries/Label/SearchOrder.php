<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class SearchOrder implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "select distinct ordhead.order_no as order_number, sups.sup_name as supplier_name, 
        		ordhead.ORIG_APPROVAL_DATE as approved_date, ordhead.status
              	from ordhead
    	  		inner join ordloc on ordhead.order_no = ordloc.order_no 
                inner join sups on sups.supplier = ordhead.supplier";

        return $this;
    }

    public function forSupplier()
    {
        $this->sql .= " inner join sup_traits_matrix on ordhead.supplier = sup_traits_matrix.supplier and sup_traits_matrix.sup_trait = :supplier_trait and ordhead.supplier = :supplier";

        return $this;
    }

    public function filter($param = 'C')
    {
        if (strtoupper($param) == 'C') {
            $this->sql .= " where ordloc.QTY_Ordered > 0 and (ordhead.status = 'A' or ordhead.status = 'C')";
        } else {
            $this->sql .= " where ordloc.QTY_Ordered > 0 and (ordhead.status = 'A')";
        }

        return $this;
    }

    public function getSql()
    {
        $this->sql .= " and ordhead.order_no = :order_no";

        return $this->sql;
    }
}
