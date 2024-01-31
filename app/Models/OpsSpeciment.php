<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsSpeciment extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'tgl_speciment',
        'file'
    ];

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function getBranch()
    {
        return $this->branches->branch_name;
    }
}
