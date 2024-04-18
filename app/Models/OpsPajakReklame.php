<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OpsPajakReklame extends Model
{
    use HasFactory, Searchable, LogsActivity;

    protected $fillable = [
        'branch_id',
        'no_izin',
        'nilai_pajak',
        'periode_awal',
        'periode_akhir',
        'note',
        'additional_info',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->useLogName("OpsPajakReklame");
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function getBranchCode()
    {
        return $this->branches->branch_code;
    }

    public function getBranchName()
    {
        return $this->branches->branch_name;
    }
}
