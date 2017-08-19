<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /**
     * [$table set name of the table]
     * @var string
     */
    protected $table = "wh";

    /**
	 * [$primaryKey description]
	 * @var string
	 */
	protected $primaryKey = 'wh';

	//turn off timestamps
	public $timestamps = false;

	/**
	 * [scopePrimary selecting primary warehouse by using null channel id]
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function scopePrimary($query)
	{
		return $query->whereNull('channel_id');
	}

	/**
	 * [scopeSearch search warehouse for user creation]
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function scopeSearch($query, $term)
	{
		return $query->where('wh_name','LIKE', '%'.strtoupper($term).'%')
					 ->orWhere('wh_name','LIKE','%'.ucfirst($term).'%')
					 ->orWhere('wh_name','LIKE','%'.strtolower($term).'%');
	}

}
