<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    public $timestamps = false;
    protected $fillable = [
        'mesa_id', 'fecha', 'hora', 'subtotal', 'total', 'estado', 'observaciones'
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }
}
