<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class GapPerdin extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'divisi_pembebanan',
        'periode',
        'user',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapPerdin");
    }

    public function gap_perdin_details()
    {
        return $this->hasMany(GapPerdinDetail::class, 'gap_perdin_id');
    }



}
