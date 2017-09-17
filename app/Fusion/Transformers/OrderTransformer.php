<?php

namespace App\Fusion\Transformers;

use Carbon\Carbon;

class OrderTransformer extends Transformer
{
    public function transform($order)
    {
        if ($this->user->isWarehouse()) {
            return [
                'order_number' => $order->order_number,
                'supplier' => $order->supplier_name,
                'approval_date' => Carbon::parse($order->approved_date)->toDayDateTimeString(),
            ];
        } else {
            return [
                'order_number' => $order->order_number,
                'supplier' => $order->supplier_name,
                'approval_date' => Carbon::parse($order->approved_date)->toDayDateTimeString(),
                // 'reprint' => $order->reprint_required ? $order->reprint_required:'Y'
            ];
        }
    }
}
