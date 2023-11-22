<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapScoringAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'entity',
        'description',
        'pic',
        'dokumen_perintah_kerja',
        'vendor',
        'tgl_scoring',
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
