<?php

namespace App\Models;

use App\Models\BranchType;
use App\Models\Employee;
use App\Models\OpsPajakReklame;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Branch extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'branch_type_id',
        'branch_code',
        'branch_name',
        'address',
        'telp',
        'layanan_atm',
        'npwp',
        'nitku',
        'izin',
        'status',
        'masa_sewa',
        'open_date',
        'expired_date',
        'owner',
        'sewa_per_tahun',
        'total_biaya_sewa',
    ];

    public function branch_types()
    {
        return $this->belongsTo(BranchType::class, 'branch_type_id', 'id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }

    public function ops_pajak_reklames()
    {
        return $this->hasOne(OpsPajakReklame::class, 'branch_id');
    }

    public function ga_izins()
    {
        return $this->hasMany(GaIzin::class, 'branch_id');
    }
}
