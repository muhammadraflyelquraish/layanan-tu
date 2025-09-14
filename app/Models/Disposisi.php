<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    use HasFactory;

    protected $table = 't_disposisi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'urutan',
    ];

    function approvers()
    {
        return $this->hasMany(DisposisiRole::class, 'disposisi_id', 'id')->orderBy("created_at", "desc");
    }
}
