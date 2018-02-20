<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Fusion\Queries\Item\Item;
use App\Fusion\Transformers\itemTransformer;
use DB;

class ItemController extends ApiController
{
   /**
     * __construct
     * @param $orderTransformer
     * @param $orderdetailTransformer
     */
    public function __construct(itemTransformer $itemTransformer)
    {
        $this->itemTransformer = $itemTransformer;
        $this->item = new Item();
    }

    /**
     * Returns item detail
     * @param  $item_no
     * @return
     */
    public function index($item_no)
    { 
        $item_query = $this->item->query()->filter('item')->getSql();
        $items = DB::select($item_query, [':item_number' => $item_no]);
        $data = $this->itemTransformer->transformCollection($items);
        return $this->respond(['data' => $data]);
    }

    /**
     * Returns item detail
     * @param  $barcodes
     * @return
     */
    public function barcode($barcode)
    { 
        $item_query = $this->item->query()->filter('barcode')->getSql();
        $items = DB::select($item_query, [':barcode' => +$barcode]);
        $data = $this->itemTransformer->transformCollection($items);
        return $this->respond(['data' => $data]);
    }
}
