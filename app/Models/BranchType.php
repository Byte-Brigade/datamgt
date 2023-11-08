<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_name',
        'alt_name'
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
