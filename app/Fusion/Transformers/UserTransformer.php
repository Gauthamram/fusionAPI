<?php

namespace App\Fusion\Transformers;


class UserTransformer extends Transformer
{
	
	public function transform($userSetting)
	{
		return [
			'name' => $userSetting->name,
			'date' => $ticket->createdate,
			'print required' => $ticket->reprint_required,
			'order' => $ticket->order_no,
		];
	}
}		