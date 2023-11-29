<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KdoMobilBiayaSewa extends Model
{
    use HasFactory;

    protected $fillable = [
        'gap_kdo_id',
        'periode',
        'value',

    ];

    public function gap_kdos()
    {
        $this->belongsTo(GapKdo::class, 'gap_kdo_id', 'id');
    }
}
