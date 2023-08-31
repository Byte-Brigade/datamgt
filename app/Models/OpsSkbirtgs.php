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
        'penerima_kuasa_1',
        'penerima_kuasa_2',
        'status'
    ];

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function getBranch()
    {
        return $this->branches->branch_name;
    }

    public function penerima_kuasa_1()
    {
        return $this->belongsTo(Employee::class, 'penerima_kuasa_1', 'id');
    }

    public function penerima_kuasa_2()
    {
        return $this->belongsTo(Employee::class, 'penerima_kuasa_2', 'id');
    }

    public function getEmployeePenerimaKuasa2()
    {
        return $this->penerima_kuasa_2->name;
    }
}
