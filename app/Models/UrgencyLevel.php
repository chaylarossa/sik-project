<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrgencyLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'color',
        'is_high_priority',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_high_priority' => 'boolean',
    ];
}
