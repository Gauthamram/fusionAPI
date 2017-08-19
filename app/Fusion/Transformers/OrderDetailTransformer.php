<?php

namespace App\Fusion\Transformers;

use Carbon\Carbon;

class OrderDetailTransformer extends Transformer
{
	
	public function transform($order)
	{
		return [
			'order_no' => $order->order_no,
			'style' => $order->item_parent,
			'item' => $order->item,
			'location'=> $order->location,
			'location_type' => $order->loc_type,
			'qty' => $order->qty_ordered,
			'country' => $order->origin_country_id,
			'retail' => $order->unit_retail,
			'pack' => $order->pack_ind,
			'simple_pack_ind' => $order->simple_pack_ind
		];	
	}
}