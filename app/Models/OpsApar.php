<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsApar extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'expired_date',
        'keterangan'
    ];


    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function getBranch()
    {
        return $this->branches->branch_name;
    }

    public function detail()
    {
        return $this->hasMany(OpsAparDetail::class, 'ops_apar_id');
    }
}
