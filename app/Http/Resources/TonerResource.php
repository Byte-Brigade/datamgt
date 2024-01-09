<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TonerResource extends JsonResource
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
            'branch_id' => $this->branch_id,
            'invoice' => $this->invoice,
            'idecice_date' => $this->idecice_date,
            'cartridge_order' => $this->cartridge_order,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'branches' => $this->branches,
        ];
    }
}
