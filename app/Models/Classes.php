<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prof',
        'code',
        'uid',
        'color',
    ];

    // RELATIONSHIPS
    public function classesByUser(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'uid');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function($model) {            
            $pastelColors = [
                '#A73741', '#AD8C41', '#AEAF41', '#649C7B', '#617DA8', '#8C67A8', '#AD6BB8'
            ];

            $model->color = Arr::random($pastelColors);
            $model->uid = auth()->user()->id;
        });
    }
}
