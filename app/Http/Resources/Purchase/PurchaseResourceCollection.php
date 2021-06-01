<?php

namespace App\Http\Resources\Purchase;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Purchase\PurchaseResource;

class PurchaseResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => PurchaseResource::collection($this->collection)
        ];
    }
}
