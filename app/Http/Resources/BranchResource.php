<?php

namespace App\Http\Resources;

use App\Models\Branch;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Branch $this */
        $branch_types = $this->branch_types;
        $branch_types['type_name'] = $branch_types->type_name == 'KF' ? 'KFO & KFNO' : $branch_types->type_name;


        return [
            'id' => $this->id,
            'branch_type_id' => $this->branch_type_id,
            'branch_code' => $this->branch_code,
            'branch_name' => $this->branch_name,
            'status' => $this->status,
            'masa_sewa' => isset($this->masa_sewa) ? $this->masa_sewa . ' Tahun' : '-',
            'open_date' => $this->open_date,
            'expired_date' => $this->expired_date,
            'nitku' => $this->nitku,
            'owner' => $this->owner,
            'izin' => $this->izin,
            'sewa_per_tahun' => $this->sewa_per_tahun,
            'nilai_pembelian' => $this->status == 'Milik' ? number_format($this->total_biaya_sewa, 0, ',', '.') : '-',
            'nilai_sewa' => $this->status != 'Milik' ? number_format($this->total_biaya_sewa, 0, ',', '.') : '-',
            'npwp' => $this->npwp,
            'address' => $this->address,
            'jumlah_karyawan' => $this->employees->count() > 0 ? $this->employees->count() . ' Orang' : 'Tidak Ada',
            // 'perizinan' => $this->gap_disnaker->map(function ($izin) {
            //     return $izin->jenis_perizinan->name;
            // })->toArray(),
            'perizinan' => $this->gap_disnaker->count() > 0
                ? 'Ada' : 'Tidak Ada',
            'kdo_mobil' => $this->gap_kdo_mobil->count(),
            'telp' => $this->telp,
            'fasilitas_atm' => isset($this->layanan_atm) && $this->layanan_atm != 'Tidak Ada' ? 'Ada' : 'Tidak Ada',
            'layanan_atm' => isset($this->layanan_atm) ? $this->layanan_atm : 'Tidak Ada',
            'branch_types' => $branch_types,
            'photo' => $this->photo,
            'bm' => $this->employees->where('position_id', 1)->pluck('name')->first()
        ];
    }
}
