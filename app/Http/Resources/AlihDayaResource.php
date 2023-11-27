<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AlihDayaResource extends JsonResource
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
            'jenis_pekerjaan' => $this->jenis_pekerjaan,
            'nama_pegawai' => $this->nama_pegawai,
            'user' => $this->user,
            'lokasi' => $this->lokasi,
            'vendor' => $this->vendor,
            'cost' => $this->cost,

        ];
    }
}
