<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class GapAlihDaya extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'jenis_pekerjaan',
        'nama_pegawai',
        'user',
        'lokasi',
        'vendor',
        'cost',
        'periode',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapAlihDaya");
    }

    public function branches() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
