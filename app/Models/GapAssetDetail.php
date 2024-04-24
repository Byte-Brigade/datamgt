<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class GapAssetDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'asset_number',
        'gap_hasil_sto_id',
        'status',
        'semester',
        'periode',
        'keterangan',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapAssetDetail");
    }

    public function gap_assets()
    {
       return  $this->belongsTo(GapAsset::class, 'asset_number', 'asset_number');
    }

    public function gap_hasil_sto()
    {
        return $this->belongsTo(GapHasilSto::class, 'gap_hasil_sto_id', 'id');
    }

}
