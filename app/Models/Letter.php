<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    protected $table = 't_surat';

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
        'proposal_id',
        'disertai_dana',
        'alasan_penolakan',
        'tanggal_selesai',
        'perlu_sk',
        'pembuat_sk_id',
        'sk_id',
        'role_id',
        'prodi_id',
    ];

    function pemohon()
    {
        return $this->belongsTo(User::class);
    }

    function pembuat_sk()
    {
        return $this->belongsTo(Disposisi::class, 'pembuat_sk_id', 'id');
    }

    function dispositions()
    {
        return $this->hasMany(LetterDisposition::class, 'surat_id', 'id')->orderBy('urutan', 'desc');
    }

    function spjs()
    {
        return $this->hasMany(SPJ::class, 'surat_id', 'id')->orderBy('created_at', 'desc');
    }

    function file()
    {
        return $this->belongsTo(Media::class,  'proposal_id', 'id');
    }

    function sk()
    {
        return $this->belongsTo(Media::class,  'sk_id', 'id');
    }

    function role()
    {
        return $this->belongsTo(Role::class,  'role_id', 'id');
    }

    function prodi()
    {
        return $this->belongsTo(Prodi::class,  'prodi_id', 'id');
    }

    function ratings()
    {
        return $this->hasMany(SPJRating::class, 'surat_id', 'id')->orderBy('created_at', 'desc');
    }
}
