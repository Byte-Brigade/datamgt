<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapAssetDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_number',
        'gap_hasil_sto_id',
        'status',
        'semester',
        'periode',
        'sto',
    ];

    public function gap_assets()
    {
       return  $this->belongsTo(GapAsset::class, 'asset_number', 'asset_number');
    }

    public function gap_hasil_sto()
    {
        return $this->belongsTo(GapHasilSto::class, 'gap_hasil_sto_id', 'id');
    }

}
