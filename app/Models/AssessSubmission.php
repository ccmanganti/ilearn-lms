<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'userid',
        'classid',
        'classcode',
        'assessid',
        'item',
        'score'
    ];

    protected $casts = [
        'item' => 'array',        
    ];
}
