<?php

namespace App\Fusion\Transformers;
use Carbon\Carbon;
use App\Fusion\UserSetting;

class TicketPrintedTransformer extends Transformer
{
	
	public function transform($ticket)
	{
		$userSetting = New UserSetting();
// dd($userSetting->isWarehouse());
		if($userSetting->isWarehouse()){
			return [
				'order' => $ticket['order_no'],
				'date' => Carbon::parse($ticket['createdate'])->toDayDateTimeString(),
				'type' => $ticket['tickettype'],
				'cartons' => $ticket['quantity'],
			];
		} else {
			return [
				'id' => $ticket['ticketrequestid'],
				'date' => Carbon::parse($ticket['createdate'])->toDayDateTimeString(),
				'cartons' => ( $ticket['packcartons'] + $ticket['loosesimplecartons'] + $ticket['mixedcartons']),
				'order' => $ticket['order_no'],
			];	
		}
		
	}
}		