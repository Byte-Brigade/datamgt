<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapPks extends Model
{
    use HasFactory;
    protected $table = "gap_pks";
    protected $fillable = [
        'vendor',
        'entity',
        'type',
        'description',
        'contract_date',
        'contract_no',
        'durasi_kontrak',
        'awal',
        'akhir',
        'tahun_akhir',
        'status',
        'periode',
    ];
}
