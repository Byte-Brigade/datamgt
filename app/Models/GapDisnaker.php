<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GapDisnaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'jenis_perizinan_id',
        'tgl_pengesahan',
        'tgl_masa_berlaku',
        'progress_resertifikasi',
        'file',
        'periode'
    ];

    public function branches() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function jenis_perizinan() {
        return $this->belongsTo(JenisPerizinan::class, 'jenis_perizinan_id', 'id');
    }

}
