<?php

namespace App\Fusion\Formatter;

use App\Fusion\Formatter\XML;

class DataFormat 
{ 

	private $formatter;

	public function setFormatter($format) {

		switch (strtolower($format)) {
			case 'xml':
				$this->formatter = New XMlFormat();
				break;
			
			default:
				$this->formatter = New JsonFormat();
				break;
		}
	}

	public function format(array $data)
    {
        return $this->formatter->load($data);
    }

}