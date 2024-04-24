<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class InfraSewaGedung extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'status_kepemilikan',
        'jangka_waktu',
        'open_date',
        'jatuh_tempo',
        'owner',
        'biaya_per_tahun',
        'total_biaya',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("InfraSewaGedung");
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
