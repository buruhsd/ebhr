<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\RoleResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleResourceCollection extends ResourceCollection
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
            'data' => RoleResource::collection($this->collection)
        ];
    }
}
