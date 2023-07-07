<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'class',
        'title',
        'desc',
        'due',
        'item',
        'uid',
    ];

    protected $casts = [
        'item' => 'json',        
    ];

    public function assessmentByUser(): HasOne
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
