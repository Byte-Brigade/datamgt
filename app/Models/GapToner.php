<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GapToner extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'invoice',
        'idecice_date',
        'cartridge_order',
        'quantity',
        'price',
        'total',
        'periode',

    ];


    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
