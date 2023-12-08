<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mpociot\Versionable\VersionableTrait;

class GapAlihDaya extends Model
{
    use HasFactory, VersionableTrait;

    protected $fillable = [
        'jenis_pekerjaan',
        'nama_pegawai',
        'user',
        'lokasi',
        'vendor',
        'cost',
    ];
}
