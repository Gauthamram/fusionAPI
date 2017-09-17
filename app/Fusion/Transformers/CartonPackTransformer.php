<?php

namespace App\Fusion\Transformers;

class CartonPackTransformer extends Transformer
{
    public function transform($order)
    {
        return [
            'order_no' => $order->order_no,
            'style' => $order->style,
            'item_number' => $order->style,
            'reprint' => $order->reprint_required
        ];
    }
}
