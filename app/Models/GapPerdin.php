<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GapPerdin extends Model
{
    use HasFactory;

    protected $fillable = [
        'divisi_pembebanan',
        'category',
        'periode',
        'value',
        'tipe',
        'user',
    ];

    public function gap_perdin_details()
    {
        return $this->hasMany(GapPerdin::class, 'gap_perdin_id');
    }



}
