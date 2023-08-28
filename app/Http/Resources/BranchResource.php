<?php

namespace App\Http\Resources;

use App\Models\Branch;
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
        /** @var Branch $this */
        return [
            'id' => $this->id,
            'branch_code' => $this->branch_code,
            'branch_name' => $this->branch_name,
            'address' => $this->address,
        ];
    }
}
