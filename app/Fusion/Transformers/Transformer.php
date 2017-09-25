<?php

namespace App\Fusion\Transformers;

use JWTAuth;

abstract class Transformer
{
    protected $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function TransformCollection(array $items, $pagination = false)
    {
        if ($pagination) {
            $data = array_map([$this, 'transform'], $items['data']);
        
            $items_data = array_replace($items, ['data' => $data]);
        
            return $items_data;
        } else {
            return array_map([$this, 'transform'], $items);
        }
    }

    abstract public function transform($item);
}
