<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 't_user_role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'role_id',
        'prodi_id',
    ];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
