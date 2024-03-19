<?php

namespace App\Http\Resources\Inquery;

use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapHasilSto;
use App\Models\GapSto;
use Carbon\Carbon;
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

        $latestPeriode = $this->branches->gap_assets->max('periode');
        $periode = GapSto::max('periode');

        $current_sto = GapSto::where('status', 'On Progress')->where('periode', $periode)->first();
        $hasil_sto = null;
        if (isset($current_sto)) {

            $hasil_sto = $this->branches->gap_hasil_stos()->where('gap_sto_id', $current_sto->id)->first();
        }

        $latestPeriode = GapSto::where('status', 'On Progress')->latest()->first();
        $prevSTO = GapSto::where('status', 'Done')->latest()->first();

        $gap_asset = null;

        if (!isset($prevSTO)) {
            $gap_asset = $this->branches->gap_assets();
        } else {
            $gap_asset = GapAsset::where('branch_id',$this->branches->id)->whereHas('gap_asset_detailS', function($q) use($prevSTO) {
                return $q->where('periode', $prevSTO->periode)->where('semester', $prevSTO->semester)
                ->where('status','Ada');
            })->get();
        }




        return [
            'id' => $this->branches->id,
            'branch_name' => $this->branches->branch_name,
            'branch_code' => $this->branches->branch_code,
            'type_name' => $this->branches->branch_types->type_name,
            'slug' => $this->branches->slug,
            'depre' => $this->branches->gap_assets()->where('category', 'Depre')->whereHas('gap_asset_details', function ($q) use ($current_sto) {

                return $q->where('periode', $current_sto->periode)->where('semester',$current_sto->semester);
            })->count() . '/' . $gap_asset->where('category', 'Depre')->count(),
            'non_depre' => $this->branches->gap_assets()->where('category', 'Non-Depre')->whereHas('gap_asset_details', function ($q) use ($current_sto) {

                return $q->where('periode', $current_sto->periode)->where('semester',$current_sto->semester);
            })->count() . '/' . $gap_asset->where('category', 'Non-Depre')->count(),
            'total_remarked' => $this->branches->gap_assets()->whereHas('gap_asset_details', function ($q) use ($current_sto) {

                return $q->where('periode', $current_sto->periode)->where('semester',$current_sto->semester);
            })->count() . '/' . $gap_asset->count(),
            'remarked' => isset($hasil_sto) ? $hasil_sto->remarked : 0,
            'disclaimer' => isset($hasil_sto) ? $hasil_sto->disclaimer : null,
            'periode' => Carbon::parse($latestPeriode->periode)->year,
            'semester' => $latestPeriode->semester,

        ];
    }
}
