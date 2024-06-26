<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class GapAsset extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'branch_id',
        'category',
        'asset_number',
        'asset_description',
        'date_in_place_service',
        'asset_cost',
        'accum_depre',
        'asset_location',
        'minor_category',
        'major_category',
        'depre_exp',
        'net_book_value',
        'remark',
        'tgl_awal_susut',
        'tgl_akhir_susut'

    ];

    protected $attributes = [
        'net_book_value' => 0,
        'asset_cost' => 0,
        'accum_depre' => 0,
        'depre_exp' => 0,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("GapAsset");
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function gap_asset_details()
    {
        return $this->hasMany(GapAssetDetail::class, 'asset_number', 'asset_number');
    }

}
