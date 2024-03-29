<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $null = [
            'id' => 0,
            'name' => null
        ];
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'branch' => $this->branch,
            'is_access_product' => $this->is_access_product,
            'is_access_price' => $this->is_access_price,
            'role' => $this->roles()->first() ? $this->roles()->first() : $null
        ];
    }
}
