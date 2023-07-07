<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'userid',
        'name',
        'email',
        'code',
        'uid',
    ];

    public function studentsOfProf(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'uid');
    }
}
