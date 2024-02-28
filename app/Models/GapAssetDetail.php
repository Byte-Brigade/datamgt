<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapAssetDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'gap_asset_id',
        'status',
        'semester',
        'periode',
        'sto',
    ];

    public function gap_assets()
    {
        $this->belongsTo(GapAsset::class, 'gap_asset_id', 'id');
    }

}
