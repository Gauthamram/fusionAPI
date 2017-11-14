<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierTrait extends Model
{
    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'sup_trait';

    //turn off timestamps
    public $timestamps = false;
    
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'sup_traits_matrix';
}
