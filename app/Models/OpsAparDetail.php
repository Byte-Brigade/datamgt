<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsAparDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'titik_posisi',
        'expired_date'
    ];


    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function getBranch()
    {
        return $this->branches->branch_name;
    }

    public function ops_apar()
    {
        return $this->belongsTo(OpsApar::class, 'ops_apar_id', 'id');
    }
}
