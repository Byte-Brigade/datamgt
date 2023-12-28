<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GapPerdin extends Model
{
    use HasFactory;

    protected $fillable = [
        'divisi_pembebanan',
        'periode',
        'user',
    ];

    public function gap_perdin_details()
    {
        return $this->hasMany(GapPerdinDetail::class, 'gap_perdin_id');
    }



}
