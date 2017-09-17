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
    public function TransformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }


    abstract public function transform($item);
}
