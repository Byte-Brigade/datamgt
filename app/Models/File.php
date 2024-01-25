<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_name',
        'filename',
        'path',
        'status',
    ];

    public function user()
    {
       return  $this->belongsTo(User::class, 'user_id', 'id');
    }
}
