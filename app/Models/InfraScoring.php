<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class InfraScoring extends Model
{
    use HasFactory, LogsActivity;

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
        'reason',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("InfraScoring");
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
