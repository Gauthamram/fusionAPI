<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'supplier';

    //turn off timestamps
    public $timestamps = false;
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'sups';

    /**
     * [orders of teh supplier]
     * @return [type] [description]
     */
    public function orders()
    {
        return $this->hasMany('App\Order', 'supplier');
    }

    public function address()
    {
        return $this->hasMany('App\Address', 'key_value_1');
    }

    public function addressType($type)
    {
        return $this->hasOne('App\Address', 'key_value_1')->type($type);
    }

    public function tickets()
    {
        return $this->hasManyThrough('App\TicketsPrinted', 'App\Order', 'supplier', 'order_no', 'supplier');
    }
    
    /**
     * [scopeActive description]
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function scopeActive($query)
    {
        return $query->where('sup_status', 'A');
    }

    /**
     * [scopeActive description]
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('sup_name', 'LIKE', '%'.strtoupper($term).'%')
                     ->orWhere('sup_name', 'LIKE', '%'.ucfirst($term).'%')
                     ->orWhere('sup_name', 'LIKE', '%'.strtolower($term).'%');
    }
}
