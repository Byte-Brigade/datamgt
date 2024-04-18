<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class InfraBro extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'branch_name',
        'branch_type',
        'category',
        'status',
        'target',
        'jatuh_tempo_sewa',
        'start_date',
        'all_progress',
        'periode',
        'gedung',
        'layout',
        'kontraktor',
        'line_telp',
        'tambah_daya',
        'renovation',
        'inventory_non_it',
        'barang_it',
        'asuransi',
        'keterangan',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("InfraBro");
    }
}
