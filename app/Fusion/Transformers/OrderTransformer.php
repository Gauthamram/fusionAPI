<?php

namespace App\Fusion\Transformers;

use Carbon\Carbon;
use App\Fusion\UserSetting;

class OrderTransformer extends Transformer
{
	
	public function transform($order)
	{
		$userSetting = New UserSetting();
		if($userSetting->isWarehouse()){
			return [
				'order_no' => $order->order_no,
				'supplier' => $order->sup_name,
				'approval_date' => Carbon::parse($order->orig_approval_date)->toDayDateTimeString(),
			];
		} else {
			return [
				'order_no' => $order->order_no,
				'supplier' => $order->sup_name,
				'approval_date' => Carbon::parse($order->orig_approval_date)->toDayDateTimeString(),
				// 'reprint' => $order->reprint_required ? $order->reprint_required:'Y'
			];	
		}
	}
}