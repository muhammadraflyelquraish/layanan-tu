<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPJDocument extends Model
{
    use HasFactory;

    protected $table = 't_spj_document';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spj_id',
        'spj_category_id',
        'file_id',
        'link'
    ];

    function file()
    {
        return $this->belongsTo(Media::class,  'file_id', 'id');
    }

    function category()
    {
        return $this->belongsTo(SPJCategory::class,  'spj_category_id', 'id');
    }
}
