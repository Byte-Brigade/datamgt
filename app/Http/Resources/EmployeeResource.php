<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'branch' => $this->branch_id,
            'position' => $this->position_id,
            'employee_id' => $this->employee_id,
            'name' => $this->name,
            'email' => strtolower(strtok($this->email, '@')),
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'hiring_date' => $this->hiring_date,
            'branches' => $this->branches,
            'employee_positions' => $this->employee_positions
        ];
    }
}
