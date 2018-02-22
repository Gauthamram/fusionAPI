<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * [$table]
     * @var string
     */
    protected $table= "addr";

    public function scopeCountry($query)
    {
        return $query->join('country', 'country.country_id', '=', 'addr.country_id');
    }
}