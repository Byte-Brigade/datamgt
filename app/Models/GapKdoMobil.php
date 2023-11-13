<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapKdoMobil extends Model
{
    use HasFactory;
    protected $casts = [
        'biaya_sewa' => 'json',
    ];
    protected $fillable = [
        'branch_id',
        'gap_kdo_id',
        'vendor',
        'nopol',
        'awal_sewa',
        'akhir_sewa',
        'biaya_sewa',
    ];

    public function gap_kdo()
    {
        return $this->belongsTo(GapKdo::class, 'gap_kdo_id','id');
    }

    public function biaya_sewas()
    {
        return $this->hasMany(KdoMobilBiayaSewa::class, 'gap_kdo_mobil_id');
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
