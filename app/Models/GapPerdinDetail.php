<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class GapPerdinDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'gap_perdin_id',
        'periode',
        'value',
        'tipe',
        'category',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapPerdinDetail");
    }

    public function gap_perdins()
    {
        return $this->belongsTo(GapPerdin::class, 'gap_perdin_id', 'id');
    }
}
