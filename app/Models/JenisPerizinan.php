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

    public function ga_izins()
    {
        return $this->hasMany(GaIzin::class, 'jenis_perizinan_id');
    }
}
