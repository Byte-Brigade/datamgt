<?php

namespace App\Http\Resources\Inquery;

use App\Models\Branch;
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

        $latestPeriode = $this->gap_assets->max('periode');
        $periode = GapSto::max('periode');

        $sto = GapSto::where('status', 'On Progress')->where('periode', $periode)->first();
        $hasil_sto = null;
        if (isset($sto)) {

            $hasil_sto = $this->gap_hasil_stos()->where('gap_sto_id', $sto->id)->first();
        }

        $latestPeriode = GapSto::where('status', 'On Progress')->max('periode');
        $prevPeriode = GapSto::where('status', 'Done')->max('periode');

        $gap_asset = $this->gap_assets()->whereHas('gap_asset_details', function ($q) use ($prevPeriode) {

            return $q->where('periode', $prevPeriode);
        });

        if (!isset($prevPeriode)) {
            $gap_asset = $this->gap_assets();
        }

        return [
            'id' => $this->id,
            'branch_name' => $this->branch_name,
            'branch_code' => $this->branch_code,
            'type_name' => $this->branch_types->type_name,
            'slug' => $this->slug,
            'depre' => $this->gap_assets()->where('category', 'Depre')->whereHas('gap_asset_details', function ($q) use ($latestPeriode) {

                return $q->where('periode', $latestPeriode);
            })->count() . '/' . $this->gap_assets->where('category', 'Depre')->count(),
            'non_depre' => $this->gap_assets()->where('category', 'Non-Depre')->whereHas('gap_asset_details', function ($q) use ($latestPeriode) {

                return $q->where('periode', $latestPeriode);
            })->count() . '/' . $this->gap_assets->where('category', 'Non-Depre')->count(),
            'total_remarked' => $this->gap_assets()->whereHas('gap_asset_details', function ($q) use ($latestPeriode) {

                return $q->where('periode', $latestPeriode);
            })->count() . '/' . $this->gap_assets->count(),
            'remarked' => isset($hasil_sto) ? $hasil_sto->remarked : 0,
            'disclaimer' => isset($hasil_sto) ? $hasil_sto->disclaimer : null,
            'periode' => Carbon::parse($sto->periode)->year,
            'semester' => $sto->semester,
        ];
    }
}
