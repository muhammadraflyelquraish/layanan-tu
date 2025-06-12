<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPJ extends Model
{
    use HasFactory;

    protected $table = 't_spj';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'letter_id',
        'user_id',
        'jenis',
        'status',
        'tanggal_proses',
        'tanggal_selesai',
        'catatan',
    ];

    function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function documents()
    {
        return $this->hasMany(SPJDocument::class, 'spj_id', 'id');
    }

    function ratings()
    {
        return $this->hasMany(SPJRating::class, 'spj_id', 'id')->orderBy('created_at', 'desc');
    }

    function histories()
    {
        return $this->hasMany(SPJHistory::class, 'spj_id', 'id')->orderBy('created_at', 'asc');
    }
}
