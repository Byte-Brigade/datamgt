<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPerizinan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'keterangan'
    ];

    public function gap_disnaker()
    {
        return $this->hasMany(GapDisnaker::class, 'jenis_perizinan_id');
    }
}
