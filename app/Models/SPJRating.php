<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPJRating extends Model
{
    use HasFactory;

    protected $table = 't_spj_rating';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spj_id',
        'user_id',
        'rating',
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
