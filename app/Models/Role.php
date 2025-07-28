<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 't_role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_disposition',
        'is_allow_deleted',
    ];

    function permissions()
    {
        return $this->hasMany(RolePermission::class)->orderBy('created_at', 'asc');
    }
}
