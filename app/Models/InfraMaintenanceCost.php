<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InfraMaintenanceCost extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'nama_project',
        'entity',
        'category',
        'jenis_pekerjaan',
        'nilai_oe_interior',
        'nilai_oe_me',
        'total_oe',
        'nama_vendor',
        'nilai_project_memo',
        'nilai_project_final',
        'kerja_tambah_kurang',
        'keterangan',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("InfraMaintenanceCost");
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
