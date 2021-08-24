<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class video extends Model
{
    //use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'videoid', 'titulo', 'descripcion', 'fecha', 'imagen', 'idlistarep', 'actualizado'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'actualizado' => 'datetime',
    ];
}