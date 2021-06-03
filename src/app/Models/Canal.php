<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canal extends Model
{
    //use HasFactory;
    protected $fillable = [
        'channelid', 'tipo', 'nombre', 'descripcion', 'creado', 'imagenes'
    ];

    protected $casts = [
        'creado' => 'datetime',
    ];
}
