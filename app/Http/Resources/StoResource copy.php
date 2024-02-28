<?php

namespace App\Http\Resources;

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

        $latestPeriode = $branch->gap_assets->max('periode');

        return [
            'id' => $this->id,
            'branch_name' => $this->branches->branch_name,
            'branch_code' => $this->branches->branch_code,
            'type_name' => $this->branches->branch_types->type_name,
            'slug' => $this->branches->slug,
            'remarked' => $this->remarked,
            'disclaimer' => $this->disclaimer,
            'periode' => $this->periode,
            'depre' => $this->gap_assets->where('periode', $latestPeriode)->where('category', 'Depre')->whereNotNull('remark')->count() . '/' . $this->gap_assets->where('periode',$latestPeriode)->where('category', 'Depre')->count(),
            'non_depre' => $this->gap_assets->where('periode', $latestPeriode)->where('category', 'Non-Depre')->whereNotNull('remark')->count() . '/' . $this->gap_assets->where('periode',$latestPeriode)->where('category', 'Non-Depre')->count(),
            'total_remarked' => $this->gap_assets->where('periode',$latestPeriode)->whereNotNull('remark')->count() . '/' . $this->gap_assets->where('periode',$latestPeriode)->count(),
        ];
    }
}
