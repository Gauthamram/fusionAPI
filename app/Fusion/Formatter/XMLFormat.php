<?php

namespace App\Fusion\Formatter;

use App\Fusion\Interfaces\FormatInterface;

class XMLFormat implements FormatInterface
{

	public $xml_data;

	public function __construct() {

		$this->xml_data = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><Label/>");
	}

	public function load($data) {
		$return = $this->array_to_xml($data, $this->xml_data);

		return $return;
	}

	public function array_to_xml($data, &$xml_data)
	{
		foreach( $data as $key => $value ) {

            if( is_array($value) ) {
            	if(is_numeric($key)) {
            		$subnode = $xml_data;
                	$this->array_to_xml($value, $subnode);
            	} else {
            		$subnode = $xml_data->addChild($key);
            		$this->array_to_xml($value, $subnode);
            	}
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
        return $xml_data->asXML();
	}
}