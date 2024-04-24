<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class GapDisnaker extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'jenis_perizinan_id',
        'tgl_pengesahan',
        'tgl_masa_berlaku',
        'progress_resertifikasi',
        'file',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapDisnaker");
    }

    public function branches() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function jenis_perizinan() {
        return $this->belongsTo(JenisPerizinan::class, 'jenis_perizinan_id', 'id');
    }

}
