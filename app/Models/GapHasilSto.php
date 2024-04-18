<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class GapHasilSto extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'gap_sto_id',
        'remarked',
        'disclaimer'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapHasilSTO");
    }

    public function gap_stos()
    {
        return $this->belongsTo(GapSto::class, 'gap_sto_id', 'id');
    }
    public function detail_assets()
    {
        return $this->hasMany(GapAssetDetail::class, 'gap_hasil_sto_id')->onDelete('cascade');
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
