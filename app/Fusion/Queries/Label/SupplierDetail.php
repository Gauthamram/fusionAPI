<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class SupplierDetail implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT sups.supplier as id, sups.sup_name as name, addr_type, addr.contact_name, 
        		addr.contact_phone, addr.contact_fax, addr.contact_email ,addr.add_1 as address_1, 
        		addr.add_2 as address_2, addr.add_3 as address_3, 
            	addr.post, addr.city, addr.state, Country.Country_Desc as country_code 
          		from sups 
              	inner join addr on addr.module = 'SUPP' and addr.key_value_1 = sups.supplier
              	inner join add_type on add_type.address_type = addr.addr_type and add_type.type_desc = :type
              	inner join country on addr.country_id = country.country_id";

        return $this;
    }

    public function forSupplier()
    {
        $this->sql .= " where sups.supplier = :supplier";

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
