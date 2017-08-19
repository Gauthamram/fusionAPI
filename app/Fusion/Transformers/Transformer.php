<?php

namespace App\Fusion\Transformers;


abstract class Transformer
{
	
	public function TransformCollection(array $items)
	{
		return array_map([$this, 'transform'], $items);
	}


	public abstract function transform($item);
}