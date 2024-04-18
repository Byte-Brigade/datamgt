<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class GapPks extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "gap_pks";
    protected $fillable = [
        'vendor',
        'entity',
        'type',
        'description',
        'contract_date',
        'contract_no',
        'durasi_kontrak',
        'awal',
        'akhir',
        'tahun_akhir',
        'status',
        'periode',
        'renewal',
        'end_contract',
        'need_update',
        'on_progress'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapPks");
    }
}
