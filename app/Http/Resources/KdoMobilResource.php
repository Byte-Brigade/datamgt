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
        $periode = null;
        if (!is_null($request->month) && !is_null($request->year)) {
            $paddedMonth = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Create a Carbon instance using the year and month
            $periode = Carbon::createFromDate($request->year, $paddedMonth, 1)->format('Y-m-d');
        }
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'vendor' => $this->vendor,
            'nopol' => $this->nopol,
            'awal_sewa' => $this->awal_sewa,
            'akhir_sewa' => $this->akhir_sewa,
            'biaya_sewa' => !is_null($periode) ?  $this->biaya_sewas->where('periode', $periode)->first() : $biaya_sewa,
            'branches' => $this->branches,
            // 'periode' => $this->biaya_sewas->flatMap(function ($data) {
            //     return [strtolower(Carbon::parse($data->periode)->format('F')) => $data->value != 0 ? number_format($data->value, 0, ',', '.') : '-'];
            // }),
            // 'biaya_sewas' => $this->biaya_sewas->map(function ($data) {
            //     $data[strtolower(Carbon::parse($data->periode)->format('F'))] = $data->value != 0 ? number_format($data->value, 0, ',', '.') : '-';
            //     return $data;
            // })->filter(function ($data) {
            //     return $data->value > 0;
            // })->toArray(),
            // 'total_sewa' => number_format(collect($this->biaya_sewas)->sum('value'), 0, ',', '.')
        ];
    }
}
