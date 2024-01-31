<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_name'
    ];


    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }
}
