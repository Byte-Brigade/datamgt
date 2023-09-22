<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsSkOperasional extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'no_surat',
        'expiry_date',
        'file',
        'note'
    ];

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function getBranchCode()
    {
        return $this->branches->branch_code;
    }

    public function getBranchName()
    {
        return $this->branches->branch_name;
    }

    public function penerima_kuasa()
    {
        return $this->belongsToMany(Employee::class, 'sk_operasional_has_penerima_kuasa', 'ops_sk_operasional_id', 'employee_id');
    }
}

