<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapAssetDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_number',
        'status',
        'semester',
        'periode',
        'sto',
    ];

    public function gap_assets()
    {
        $this->belongsTo(GapAsset::class, 'asset_number', 'asset_number');
    }

}
