<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Episode extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'number', 'title', 'description', 'season_id', 'deleted_at',
    ];

    public function season()
    {
        return $this->belongsTo(Seasons::class);
    }
}
