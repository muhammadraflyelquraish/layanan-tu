<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposisiRole extends Model
{
    use HasFactory;

    protected $table = 't_disposisi_role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'disposisi_id',
        'role_id',
        'prodi_id',
    ];

    function disposisi()
    {
        return $this->belongsTo(Disposisi::class, 'disposisi_id');
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
