<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class KdoMobilResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $latest_periode = $this->biaya_sewas()->where('value', '>', 0)->orderBy('periode', 'desc')->get()->first();
        return [
            'id' => $this->id,
            'gap_kdo_id' => $this->gap_kdo_id,
            'branch_id' => $this->branch_id,
            'vendor' => $this->vendor,
            'nopol' => $this->nopol,
            'awal_sewa' => $this->awal_sewa,
            'akhir_sewa' => $this->akhir_sewa,
            'periode' => $this->biaya_sewas->flatMap(function ($data) {
                return [strtolower(Carbon::parse($data->periode)->format('F')) => $data->value != 0 ? number_format($data->value, 0, ',', '.') : '-'];
            }),
            'biaya_sewa' => $latest_periode,
            'biaya_sewas' => $this->biaya_sewas->map(function ($data) {
                $data[strtolower(Carbon::parse($data->periode)->format('F'))] = $data->value != 0 ? number_format($data->value, 0, ',', '.') : '-';
                return $data;
            })->filter(function ($data) {
                return $data->value > 0;
            })->toArray(),
            'branches' => $this->branches,
            'total_sewa' => number_format(collect($this->biaya_sewas)->sum('value'), 0, ',', '.')
        ];
    }
}
