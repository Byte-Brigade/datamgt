<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfraBro extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'branch_name',
        'branch_type',
        'activity',
        'status',
        'target',
        'jatuh_tempo_sewa',
        'all_progress',
    ];
}
