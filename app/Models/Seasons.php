<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seasons extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'number', 'description', 'tv_series_id', 'delete_at',
    ];

    public function tvSeries()
    {
        return $this->belongsTo(TvSeries::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class, 'season_id');
    }
}
