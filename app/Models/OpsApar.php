<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OpsApar extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'branch_id',
        'keterangan',
        'titik_posisi',
        'expired_date'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("OpsApar");
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
