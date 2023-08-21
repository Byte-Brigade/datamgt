<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Branch extends Model
{
    use HasFactory, Searchable;

    protected $fillable= [
        'branch_code',
        'branch_name',
        'address'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }

    public function toSearchableArray()
    {
        return [
            'branch_code' => $this->branch_code,
            'branch_name' => $this->branch_name,
            'address' => $this->address
        ];
    }
}
