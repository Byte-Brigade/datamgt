<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapKdo extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'unit',
        'jumlah_driver',
        'periode',
        'biaya_driver',
        'ot',
        'rfid',
        'non_rfid',
        'grab',
    ];


    public function gap_kdo_mobil() {
        return $this->hasMany(GapKdoMobil::class, 'gap_kdo_id');
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
