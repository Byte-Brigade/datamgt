<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class GapKdo extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'vendor',
        'nopol',
        'awal_sewa',
        'akhir_sewa',
        'biaya_sewa',
        'periode'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("Cabang");
    }

    public function biaya_sewas()
    {
        return $this->hasMany(KdoMobilBiayaSewa::class, 'gap_kdo_id');
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
