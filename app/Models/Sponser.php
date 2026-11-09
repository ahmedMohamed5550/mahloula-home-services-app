<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponser extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'desc',
        'image',
        'expired_at',
        'type',
    ];

    protected $dates = [
        'expired_at',
    ];
}
