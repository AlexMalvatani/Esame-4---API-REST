<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cripted extends Model
{

    use SoftDeletes;

    protected $table = 'cripted';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'secureString',
        'tries',
        'locked',
        'last_login_attempt',
        'created_at',
        'updated_at',
        'salt_psw',
        'random_salt',
        'token',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
