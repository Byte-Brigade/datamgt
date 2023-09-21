<?php

namespace App\Models;

use App\Models\BranchType;
use App\Models\Employee;
use App\Models\OpsPajakReklame;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Branch extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'branch_type_id',
        'branch_code',
        'branch_name',
        'address',
        'telp',
        'layanan_atm'
    ];

    public function branch_types()
    {
        return $this->belongsTo(BranchType::class, 'branch_type_id', 'id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }

    public function ops_pajak_reklames()
    {
        return $this->hasOne(OpsPajakReklame::class, 'branch_id');
    }
}
