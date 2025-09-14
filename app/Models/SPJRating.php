<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPJRating extends Model
{
    use HasFactory;

    protected $table = 't_rating';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spj_id',
        'surat_id',
        'user_id',
        'rating',
        'catatan',
        'tipe'
    ];

    function spj()
    {
        return $this->belongsTo(SPJ::class, 'spj_id');
    }

    function surat()
    {
        return $this->belongsTo(Letter::class, 'surat_id');
    }

    function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
