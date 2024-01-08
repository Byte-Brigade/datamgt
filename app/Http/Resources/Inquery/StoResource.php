<?php

namespace App\Http\Resources\Inquery;

use App\Models\Branch;
use Illuminate\Http\Resources\Json\JsonResource;

class StoResource extends JsonResource
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
            'type_name' => $this->branch_types->type_name,
            'slug' => $this->slug,
            'depre' => $this->gap_assets->where('category','Depre')->whereNotNull('remark')->count(). '/'. $this->gap_assets->where('category','Depre')->count(),
            'non_depre' => $this->gap_assets->where('category','Non-Depre')->whereNotNull('remark')->count(). '/'. $this->gap_assets->where('category','Non-Depre')->count(),
            'total_remarked' => $this->gap_assets->whereNotNull('remark')->count(). '/'. $this->gap_assets->count(),
            'remarked' => isset($this->gap_stos) ? $this->gap_stos->remarked : 0,
            'disclaimer' => isset($this->gap_stos) ? $this->gap_stos->disclaimer : null
        ];
    }
}
