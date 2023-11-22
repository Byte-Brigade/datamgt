<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfraSewaGedung extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'status_kepemilikan',
        'jangka_waktu',
        'open_date',
        'jatuh_tempo',
        'owner',
        'biaya_per_tahun',
        'total_biaya',
    ];

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
