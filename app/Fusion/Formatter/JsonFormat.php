<?php

namespace App\Fusion\Formatter;

use App\Fusion\Interfaces\FormatInterface;

class JsonFormat implements FormatInterface
{

	public function load($data) {

		if (is_array($data)) {
			return json_encode($data);
		}

	}
}