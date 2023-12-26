<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsApar extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'keterangan',
        'titik_posisi',
        'expired_date'
    ];


    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
