<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Canal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'channelid', 'nombre', 'descripcion', 'fecha', 'imagen', 'actualizado'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'actualizado' => 'datetime'
    ];
}
