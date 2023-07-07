<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'userid',
        'classid',
        'classcode',
        'taskid',
        'file',
        'score'
    ];
}
