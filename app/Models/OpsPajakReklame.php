<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class OpsPajakReklame extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'branch_id',
        'periode_awal',
        'periode_akhir',
        'note',
        'additional_info',
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
}
