<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketTypeDefault extends Model
{
    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'ticket_type_id';

    //turn off timestamps
    public $timestamps = false;
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'cgl_ticket_type_defaults';

    /**
     * [printer for the defaults]
     * @return [type] [description]
     */
    public function printer()
    {
        return $this->hasOne('App\TicketTypePrinter', 'ticket_type_id');
    }
}
