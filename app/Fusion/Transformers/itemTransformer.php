<?php

namespace App\Fusion\Transformers;

use Config;

class itemTransformer extends Transformer
{
    public function transform($item)
    {

        return [
            'item_number' => $item->item_number,
            'description' => $item->short_desc,
            'colour' => $item->colour,
            'item_size' => $item->item_size,
            'barcode' => $item->barcode,
            'quantity' => $item->quantity,
            'barcode_type' => Config::get('ticket.barcodetype.'.strlen($item->barcode))
        ];
    }
}