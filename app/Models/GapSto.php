<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class GapSto extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'periode',
        'semester',
        'status',
        'keterangan',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapSto");
    }

    public function hasil_stos()
    {
        $this->hasMany(GapHasilSto::class, 'gap_sto_id')->onDelete('cascade');
    }


}
