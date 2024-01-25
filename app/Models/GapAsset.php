<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GapAsset extends Model
{
    use HasFactory;




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
        'periode',
        'remark',

    ];

    protected $attributes = [
        'net_book_value' => 0,
        'asset_cost' => 0,
        'accum_depre' => 0,
        'depre_exp' => 0,
    ];

    public function branches() {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }





}
