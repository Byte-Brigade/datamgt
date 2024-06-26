<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class KdoMobilResource extends JsonResource
{

    protected $month;
    protected $year;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */


    public function toArray($request)
    {
        $biaya_sewa = $this->biaya_sewas()->orderBy('periode', 'desc')->first();


        if (!is_null($request->input('$M')) && !is_null($request->input('$y'))) {
            $year = $request->input('$y');
            $month = ((int) $request->input('$M')) + 1;
            $date = Carbon::createFromFormat('Y-m', $year . '-' . $month);
            $biaya_sewa = $this->biaya_sewas->where('periode', $date->startOfMonth()->format('Y-m-d'))->first();
        }
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'vendor' => $this->vendor,
            'nopol' => $this->nopol,
            'awal_sewa' => $this->awal_sewa,
            'akhir_sewa' => $this->akhir_sewa,
            'periode' => isset($biaya_sewa->periode) ? Carbon::parse($biaya_sewa->periode)->format('Y-m') : Carbon::now()->format('Y-m'),
            'biaya_sewa' => isset($biaya_sewa->value) ? $biaya_sewa->value : 0,
            'branches' => $this->branches,
        ];
    }
}
