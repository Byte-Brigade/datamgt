<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KdoMobilBiayaSewa extends Model
{
    use HasFactory;

    protected $fillable = [
        'gap_kdo_mobil_id',
        'periode',
        'value',

    ];

    public function gap_kdo_mobil()
    {
        $this->belongsTo(GapKdoMobil::class, 'gap_kdo_mobil_id', 'id');
    }
}
