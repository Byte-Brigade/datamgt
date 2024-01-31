<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $position = $this->roles->flatMap(function($role) {
            return [
                'name' => $role->name,
                'alt_name' => $role->alt_name,
            ];
        });
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nik' => $this->nik,
            'position' => isset($position) ? $position : '-',
            'permissions' => $this->getAllPermissions()->map(function($permission) {
                return $permission->name;
            })->toArray(),
        ];
    }
}
