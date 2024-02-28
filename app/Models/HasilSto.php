<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilSto extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'remarked',
        'disclaimer'
    ];

    public function gap_stos()
    {
        $this->belongsTo(GapSto::class, 'gap_sto_id', 'id');
    }
}
