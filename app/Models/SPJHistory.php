<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPJHistory extends Model
{
    use HasFactory;

    protected $table = 't_spj_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spj_id',
        'user_id',
        'status',
        'catatan',
    ];

    function spj()
    {
        return $this->belongsTo(SPJ::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
