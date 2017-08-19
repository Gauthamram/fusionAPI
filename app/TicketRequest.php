<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketRequest extends Model
{
    /**
     * [$table name]
     * @var string
     */
    protected $table = 'ticket_request';
    protected $primaryKey = null;
    public $incrementing = false;

    //turn off timestamp columns
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item','ticket_type_id','qty', 'loc_type','location','unit_retail','multi_units','multi_unit_retail','country_of_origin','order_no','print_online_ind','create_datetime','last_update_datetime','last_update_id','sort_order_type','printer_type'
    ];
}
