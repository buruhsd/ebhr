<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $collection = $this->permissions()->get();
        $filtered = $collection->filter(function ($value, $key) {
            $value->label = $value->name;
            return $value;
        });
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->name,
            'guard_name' => $this->guard_name,
            'permissions' => $filtered
        ];
    }
}
