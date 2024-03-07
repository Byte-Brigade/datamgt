<?php

namespace App\Http\Resources;

use App\Models\Branch;
use App\Models\GapHasilSto;
use App\Models\GapSto;
use Illuminate\Http\Resources\Json\JsonResource;

class HasilStoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    protected $gap_sto_id;


    public function toArray($request)
    {

        $latestPeriode = $this->gap_assets->max('periode');
        $periode = GapSto::max('periode');

        $sto = GapSto::find($request->gap_sto_id);
        $hasil_sto = null;

        $hasil_sto = $this->gap_hasil_stos()->where('gap_sto_id', $sto->id)->first();
        return [
            'id' => $this->id,
            'branch_name' => $this->branch_name,
            'branch_code' => $this->branch_code,
            'type_name' => $this->branch_types->type_name,
            'slug' => $this->slug,
            'depre' => $this->gap_assets->where('periode', $latestPeriode)->where('category', 'Depre')->whereNotNull('remark')->count() . '/' . $this->gap_assets->where('periode', $latestPeriode)->where('category', 'Depre')->count(),
            'non_depre' => $this->gap_assets->where('periode', $latestPeriode)->where('category', 'Non-Depre')->whereNotNull('remark')->count() . '/' . $this->gap_assets->where('periode', $latestPeriode)->where('category', 'Non-Depre')->count(),
            'total_remarked' => $this->gap_assets->where('periode', $latestPeriode)->whereNotNull('remark')->count() . '/' . $this->gap_assets->where('periode', $latestPeriode)->count(),
            'remarked' => isset($hasil_sto) ? $hasil_sto->remarked : 0,
            'disclaimer' => isset($hasil_sto) ? $hasil_sto->disclaimer : null
        ];
    }
}
