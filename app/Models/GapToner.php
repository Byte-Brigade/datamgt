<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class GapToner extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'invoice',
        'idecice_date',
        'cartridge_order',
        'quantity',
        'price',
        'total',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapToner");
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
