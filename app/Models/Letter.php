<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    protected $table = 't_letter';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kode',
        'nomor_agenda',
        'tanggal_surat',
        'nomor_surat',
        'asal_surat',
        'hal',
        'pemohon_id',
        'tanggal_diterima',
        'untuk',
        'status',
        'proposal_file',
        'disertai_dana',
        'alasan_penolakan'
    ];

    function pemohon()
    {
        return $this->belongsTo(User::class);
    }

    function dispositions()
    {
        return $this->hasMany(LetterDisposition::class)->orderBy('created_at', 'desc');
    }

    function spjs()
    {
        return $this->hasMany(SPJ::class, 'letter_id', 'id')->orderBy('created_at', 'desc');
    }
}
