<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    public $timestamps = false;
    protected $hidden = ['contrasena', 'token'];
    protected $fillable = [
        'nombre', 'correo', 'usuario', 'contrasena', 'rol',
        'token', 'sesion_activa', 'estado'
    ];
}
