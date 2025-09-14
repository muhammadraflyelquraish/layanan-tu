<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterDisposition extends Model
{
    use HasFactory;

    protected $table = 't_surat_disposisi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'surat_id',
        'disposisi_id',
        'tanggal_diterima',
        'tanggal_diproses',
        'keterangan',
        'status',
        'urutan',
        'verifikator_id',
        'verifikator_role_id',
    ];


    function surat()
    {
        return $this->belongsTo(Letter::class, 'surat_id');
    }

    function disposition()
    {
        return $this->belongsTo(Disposisi::class, 'disposisi_id');
    }

    function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    function verifikatorRole()
    {
        return $this->belongsTo(Role::class, 'verifikator_role_id');
    }
}
