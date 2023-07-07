<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'classcode',
        'uid',
        'attendance',
    ];

    protected $casts = [
        'attendance' => 'array',        
    ];

    public function attendanceByUser(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'uid');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function($model) {
                $model->uid = auth()->user()->id;
        });
    }
}
