<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapSto extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'remarked',
        'disclaimer',
        'periode',
    ];




    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
