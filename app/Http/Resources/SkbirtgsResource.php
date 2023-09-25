<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SkbirtgsResource extends JsonResource
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
            'no_surat' => $this->no_surat,
            'branch_id' => $this->branch_id,
            'status' => $this->status,
            'file' => $this->file,
            'penerima_kuasa' =>  implode(' - ', $this->penerima_kuasa()->get()->map(function($employee) {
                return !is_null($employee->getPosition()) ? '['.$employee->getPosition().'] '.$employee->name : $employee->name;
            })->toArray()),
            'branches' => $this->branches
        ];
    }
}
