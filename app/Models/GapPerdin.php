<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mpociot\Versionable\VersionableTrait;

class GapPerdin extends Model
{
    use HasFactory, VersionableTrait;

    protected $fillable = [
        'divisi_pembebanan',
        'category',
        'periode',
        'value',
        'tipe'
    ];

}
