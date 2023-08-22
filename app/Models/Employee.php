<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class Employee extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'branch_id',
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

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'employee_id' => $this->employee_id,
            'email' => $this->email
        ];
    }
}
