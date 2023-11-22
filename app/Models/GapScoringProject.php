<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapScoringProject extends Model
{
    use HasFactory;


    protected $fillable = [
        'branch_id',
        'entity',
        'description',
        'pic',
        'status_pekerjaan',
        'dokumen_perintah_kerja',
        'vendor',
        'nilai_project',
        'tgl_selesai_pekerjaan',
        'tgl_bast',
        'tgl_request_scoring',
        'tgl_scoring',
        'sla',
        'actual',
        'meet_the_sla',
        'schedule_scoring',
        'scoring_vendor',
        'type',
        'keterangan',
    ];

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
