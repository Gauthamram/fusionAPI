<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Fusion\Queries\Item\Item;
use App\Fusion\Queries\Item\ItemBarcode;
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
    }

    /**
     * Returns item detail
     * @param  $item_no
     * @return
     */
    public function index($item_no)
    { 
    	$item = new Item();
        $item_query = $item->query()->getSql();
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
    	$item = new ItemBarcode();
        $item_query = $item->query()->getSql();
        $items = DB::select($item_query, [':barcode' => $barcode]);

        return $this->respond(['data' => $items]);
    }
}
