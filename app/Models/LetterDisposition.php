<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterDisposition extends Model
{
    use HasFactory;

    protected $table = 't_letter_disposition';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'letter_id',
        'position_id',
        'tanggal_diterima',
        'tanggal_diproses',
        'verifikator_id',
        'keterangan',
        'status',
    ];


    function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    function position()
    {
        return $this->belongsTo(Role::class, 'position_id');
    }

    function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}
