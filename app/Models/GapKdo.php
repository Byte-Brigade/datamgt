<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mpociot\Versionable\VersionableTrait;

class GapKdo extends Model
{
    use HasFactory, VersionableTrait;

    protected $fillable = [
        'branch_id',
        'vendor',
        'nopol',
        'awal_sewa',
        'akhir_sewa',
        'biaya_sewa',
        'periode'
    ];


    public function biaya_sewas()
    {
        return $this->hasMany(KdoMobilBiayaSewa::class, 'gap_kdo_id');
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
