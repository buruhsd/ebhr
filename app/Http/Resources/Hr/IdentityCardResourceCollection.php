<?php

namespace App\Http\Resources\Hr;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Hr\IdentityCardResource;

class IdentityCardResourceCollection extends ResourceCollection
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
            'data' => IdentityCardResource::collection($this->collection)
        ];
    }
}
