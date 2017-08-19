<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * [$table set name of the table]
     * @var string
     */
    protected $table = "addr";
    public $timestamps = false;
    /**
	 * [$primaryKey description]
	 * @var string
	 */
	protected $primaryKey = 'addr_key';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['contact_name','contact_phone','contact_fax','contact_email','add_1','add_2','add_3','post','city','state'];

    /**
     * [supplier description]
     * @return [object] []
     */
    public function supplier()
    {
    	$this->belongsTo('App\Supplier','key_value_1');
    }

    /**
     * [scopeType description]
     * @param  [type] $query [description]
     * @param  [type] $type  [config value for the type passed]
     * @return [type]        [description]
     */
    public function scopeType($query, $type){
    	$query->Where('addr_type',$type);
    }
}
