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
            'no_surat' => str_contains($this->no_surat, 'SK') ? $this->no_surat : '-',
            'branch_id' => $this->branch_id,
            'status' => $this->status,
            'file' => $this->file,
            'reverse' =>  $this->penerima_kuasa()->get()->count() > 0 ? $this->penerima_kuasa()->pluck('name')->toArray() : 'Central - KP',
            'target' =>  $this->penerima_kuasa()->get()->count() > 0 ? $this->penerima_kuasa()->pluck('name') : 'Central - KP',
            'penerima_kuasa' => $this->penerima_kuasa()->get()->count() > 0 ? implode(' - ', $this->penerima_kuasa()->get()->map(function($employee) {
                return '['.$employee->getPosition().']'.' '.$employee->name;
            })->toArray()): 'Central - KP',

            'branches' => $this->branches
        ];
    }
}
