<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapSto extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode',
        'semester',
        'status',
        'keterangan',
    ];

    public function hasil_stos()
    {
        $this->hasMany(GapHasilSto::class, 'gap_sto_id')->onDelete('cascade');
    }


}
