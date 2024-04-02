<?php

namespace App\Http\Resources\Inquery;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'branch_name' => $this->branch_name,
            'branch_code' => $this->branch_code,
            'area' => $this->area,
            'address' => $this->address,
            'type_name' => $this->branch_types->type_name,
            // 'bm' => $this->employees->where('position_id', 5)->pluck('name')->first(),
            'bm' => Employee::where('branch_id', $this->id)->whereHas('employee_positions', function ($q) {
                return $q->where('position_name', 'Branch Manager');
            })->pluck('name')->first(),
            'slug' => $this->slug
        ];
    }
}
