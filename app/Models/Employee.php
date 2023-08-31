<?php

namespace App\Models;

use App\Models\Branch;
use Laravel\Scout\Searchable;
use App\Models\EmployeePosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'branch_id',
        'position_id',
        'employee_id',
        'name',
        'email',
        'gender',
        'birth_date',
        'hiring_date',
    ];

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function getBranch()
    {
        return $this->branches->branch_name;
    }

    public function positions()
    {
        return $this->belongsTo(EmployeePosition::class, 'position_id', 'id');
    }

    public function getPosition()
    {
        return $this->positions->position_name;
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'employee_id' => $this->employee_id,
            'email' => $this->email,
            'branches.branch_code' => '',
            'branches.branch_name' => '',
            'employee_positions.position_name' => ''
        ];
    }

    public function ops_skbirtgs()
    {
        return $this->belongsToMany(OpsSkbirtgs::class, 'skbirtgs_has_penerima_kuasa', 'employee_id', 'ops_skbirtgs_id');
    }
}
