<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PksResource extends JsonResource
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
            'vendor' => $this->vendor,
            'type' => $this->type,
            'description' => $this->description,
            'contract_date' => $this->contract_date,
            'contract_no' => $this->contract_no,
            'durasi_kontrak' => $this->durasi_kontrak,
            'awal' => $this->awal,
            'akhir' => $this->akhir,
            'tahun_akhir' => $this->tahun_akhir,
            'status' => $this->status,
            'periode' => $this->periode,
        ];
    }
}
