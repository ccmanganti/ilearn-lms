<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'userid',
        'username',
        'classid',
        'classcode',
        'classname',
        'classprof',
        'classprofid',
        'asstype',
        'assid',
        'assname',
        'asspoints',
        'score',
    ];

    public function studentByUser(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'classprofid');
    }

    public function myGrades(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'userid');
    }
}
