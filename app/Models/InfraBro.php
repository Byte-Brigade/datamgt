<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mpociot\Versionable\VersionableTrait;

class InfraBro extends Model
{
    use HasFactory, VersionableTrait;

    protected $fillable = [
        'branch_id',
        'branch_name',
        'branch_type',
        'category',
        'status',
        'target',
        'jatuh_tempo_sewa',
        'start_date',
        'all_progress',
        'periode'
        'gedung',
        'layout',
        'kontraktor',
        'kontraktor',
        'line_telp',
        'tambah_daya',
        'renovation',
        'inventory_non_it',
        'barang_it',
        'asuransi',
    ];
}
