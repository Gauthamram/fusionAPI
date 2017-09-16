<?php

namespace App\Fusion\Transformers;
use Carbon\Carbon;

class TicketPrintedTransformer extends Transformer
{
	
	public function transform($ticket)
	{
		if($this->user->isWarehouse()){
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