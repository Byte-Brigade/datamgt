<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpsSkbirtgs extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_surat',
        'branch_id',
        'status',
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

    public function penerima_kuasa()
    {
        return $this->belongsToMany(Employee::class, 'skbirtgs_has_penerima_kuasa', 'ops_skbirtgs_id', 'employee_id');
    }
}
