<?php

namespace App\Fusion\Transformers;
use Carbon\Carbon;
use App\Fusion\UserSetting;

class TicketTransformer extends Transformer
{
	public function transform($ticket)
	{
		return [
			'order_no' => $ticket['order_no'],
			'item_number'=> $ticket['item'],
			'type' => $ticket['ticket_type_id'],
			'quantity' => $ticket['qty'],
			'date' => Carbon::parse($ticket['create_datetime'])->toDayDateTimeString(),
			'sort' => $ticket['sort_order_type']
		];		
	}
}		