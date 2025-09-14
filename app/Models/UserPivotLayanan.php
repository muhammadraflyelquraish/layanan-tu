<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPivotLayanan extends Model
{
    use HasFactory;

    protected $connection = 'mysql_layanan';
    protected $table = 'users_pivot';

    protected $fillable = [
        'id_user',
        'id_role',
        'id_program_studi',
        'id_fakultas'
    ];
}
