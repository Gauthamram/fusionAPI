<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class SupplierDetail implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT sups.supplier as id, sups.sup_name as name, addr.contact_name, 
        		addr.contact_phone, addr.contact_email 
          		from sups 
              	inner join addr on addr.module = 'SUPP' and addr.key_value_1 = sups.supplier
                inner join sup_traits_matrix on sup_traits_matrix.sup_trait = :supplier_trait and sups.supplier = sup_traits_matrix.supplier";

        return $this;
    }

    public function filter($param = '')
    {
        $this->sql .= " WHERE sups.supplier = :supplierid";

        return $this;
    }

    public function getSql()
    {
        return $this->sql;
    }
}
