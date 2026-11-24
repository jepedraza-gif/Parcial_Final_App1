<?php

namespace App\Controllers;

use App\Models\DetallePedido;
use App\Models\Pedido;
use Exception;

class PedidosController
{
    public function all(): array
    {
        return Pedido::with('detalles')->get()->toArray();
    }

    public function create(array $data): Pedido
    {
        $pedido = new Pedido();
        $pedido->mesa_id = (int)$data['mesa_id'];
        $pedido->fecha = $data['fecha'];
        $pedido->hora = $data['hora'];
        $pedido->subtotal = 0;
        $pedido->total = 0;
        $pedido->estado = $data['estado'] ?? 'pendiente';
        $pedido->observaciones = $data['observaciones'] ?? null;
        $pedido->save();

        $detalles = $data['detalles'] ?? [];
        $total = 0;
        foreach ($detalles as $detalle) {
            $cantidad = (int)$detalle['cantidad'];
            $precio_unitario = (float)$detalle['precio_unitario'];
            $subtotal_linea = $cantidad * $precio_unitario;
            $total += $subtotal_linea;

            $d = new DetallePedido();
            $d->pedido_id = $pedido->id;
            $d->producto_id = (int)$detalle['producto_id'];
            $d->nombre_producto = $detalle['nombre_producto'] ?? '';
            $d->cantidad = $cantidad;
            $d->precio_unitario = $precio_unitario;
            $d->subtotal = $subtotal_linea;
            $d->save();
        }

        $pedido->subtotal = $total;
        $pedido->total = $total;
        $pedido->save();

        return $pedido;
    }

    public function update(int $id, array $data): Pedido
    {
        $pedido = Pedido::findOrFail($id);

        if (isset($data['mesa_id'])) {
            $pedido->mesa_id = (int)$data['mesa_id'];
        }

        if (isset($data['fecha'])) {
            $pedido->fecha = $data['fecha'];
        }

        if (isset($data['hora'])) {
            $pedido->hora = $data['hora'];
        }

        if (isset($data['estado'])) {
            $pedido->estado = $data['estado'];
        }

        if (isset($data['observaciones'])) {
            $pedido->observaciones = $data['observaciones'];
        }

        $pedido->save();
        return $pedido;
    }

    public function delete(int $id): bool
    {
        $pedido = Pedido::findOrFail($id);
        return $pedido->delete();
    }
}
