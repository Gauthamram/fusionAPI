<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'order_no';

    /**
     * [$table description]
     * @var string
     */
    protected $table= "ordhead";

    /**
     * [supplier description]
     * @return [type] [description]
     */
    public function supplier()
    {
        return $this->belongsTo('App\Supplier', 'supplier');
    }

    
    /**
     * [scopeApproved description]
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function scopeApproved($query)
    {
        $query->where('status', 'A');
    }
}
