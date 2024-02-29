<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TvSeries extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title', 'description', 'actors', 'director', 'year', 'category_id', 'deleted_at',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seasons()
    {
        return $this->hasMany(Seasons::class);
    }
}
