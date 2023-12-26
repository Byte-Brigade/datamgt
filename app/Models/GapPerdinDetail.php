<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GapPerdinDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'gap_perdin_id',
        'periode',
        'value',
    ];

    public function gap_perdins()
    {
        return $this->belongsTo(GapPerdin::class, 'gap_perdin_id','id')
    }
}
