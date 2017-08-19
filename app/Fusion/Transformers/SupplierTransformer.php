<?php

namespace App\Fusion\Transformers;

use Carbon\Carbon;
class SupplierTransformer extends Transformer
{
	
	public function transform($supplier)
	{
		return [
			'id' => $supplier['supplier'],
			'name' => $supplier['sup_name'],
			'contact' => $supplier['contact_name'],
			'email' => $supplier['contact_email'],
			'phone' => $supplier['contact_phone'],
		];
	}
}