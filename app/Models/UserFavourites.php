<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFavourites extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'movies_id', 'tv_series_id', 'item_type', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'item_id');
    }

    public function tvSeries()
    {
        return $this->belongsTo(TvSeries::class, 'item_id');
    }
}
