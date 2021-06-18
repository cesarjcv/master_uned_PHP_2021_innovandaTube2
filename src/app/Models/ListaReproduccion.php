<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListaReproduccion extends Model
{
    //use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'listid', 'nombre', 'descripcion', 'fecha', 'imagen', 'idcanal', 'actualizado'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'actualizado' => 'datetime',
    ];
}
