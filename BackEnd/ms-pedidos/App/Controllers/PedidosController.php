<?php

namespace App\Controllers;

use App\Models\DetallePedido;
use App\Models\Pedido;
use Exception;

class PedidosController
{
    public function all(): array
    {
        $pedidos = Pedido::with('detalles')->get();
        $resultado = [];
        foreach ($pedidos as $pedido) {
            $arr = $pedido->toArray();
            $arr['cantidad_total'] = $pedido->detalles->sum('cantidad');
            $resultado[] = $arr;
        }
        return $resultado;
    }

    public function create(array $data): Pedido
    {
        if (empty($data['detalles']) || !is_array($data['detalles']) || count($data['detalles']) === 0) {
            throw new Exception('El pedido no puede estar vacío', 1);
        }

        foreach ($data['detalles'] as $detalle) {
            $cantidad = (int)($detalle['cantidad'] ?? 0);
            if ($cantidad < 1) {
                throw new Exception('La cantidad debe ser mayor o igual a uno', 1);
            }
        }

        // Validación de mesa disponible: el frontend debe validar esto consultando ms-reservas
        // antes de enviar el pedido. Si el frontend envía mesa_estado, se valida aquí.
        if (isset($data['mesa_estado']) && $data['mesa_estado'] === 'disponible') {
            throw new Exception('No se puede registrar un pedido para una mesa disponible', 1);
        }

        $pedido = new Pedido();
        $pedido->mesa_id = (int)$data['mesa_id'];
        $pedido->fecha = $data['fecha'];
        $pedido->hora = $data['hora'];
        $pedido->subtotal = 0;
        $pedido->total = 0;
        $pedido->estado = $data['estado'] ?? 'pendiente';
        $pedido->observaciones = $data['observaciones'] ?? null;
        $pedido->save();

        $total = 0;
        $cantidadTotal = 0;
        foreach ($data['detalles'] as $detalle) {
            $cantidad = (int)$detalle['cantidad'];
            $precio_unitario = (float)$detalle['precio_unitario'];
            $subtotal_linea = $cantidad * $precio_unitario;
            $total += $subtotal_linea;
            $cantidadTotal += $cantidad;

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

        // Agregar cantidad_total al objeto para la respuesta JSON
        $pedido->cantidad_total = $cantidadTotal;

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

    public function addDetalle(int $id, array $data): DetallePedido
    {
        $pedido = Pedido::findOrFail($id);

        $cantidad = (int)($data['cantidad'] ?? 0);
        if ($cantidad < 1) {
            throw new Exception('La cantidad debe ser mayor o igual a uno', 1);
        }

        $precio_unitario = (float)($data['precio_unitario'] ?? 0);
        $subtotal_linea = $cantidad * $precio_unitario;

        $detalle = new DetallePedido();
        $detalle->pedido_id = $pedido->id;
        $detalle->producto_id = (int)($data['producto_id'] ?? 0);
        $detalle->nombre_producto = $data['nombre_producto'] ?? '';
        $detalle->cantidad = $cantidad;
        $detalle->precio_unitario = $precio_unitario;
        $detalle->subtotal = $subtotal_linea;
        $detalle->save();

        // Recalcular totales del pedido
        $total = $pedido->detalles()->sum('subtotal');
        $pedido->subtotal = $total;
        $pedido->total = $total;
        $pedido->save();

        return $detalle;
    }

    public function updateDetalle(int $pedidoId, int $detalleId, array $data): DetallePedido
    {
        $detalle = DetallePedido::where('id', $detalleId)
            ->where('pedido_id', $pedidoId)
            ->firstOrFail();

        if (isset($data['cantidad'])) {
            $cantidad = (int)$data['cantidad'];
            if ($cantidad < 1) {
                throw new Exception('La cantidad debe ser mayor o igual a uno', 1);
            }
            $detalle->cantidad = $cantidad;
        }

        if (isset($data['precio_unitario'])) {
            $detalle->precio_unitario = (float)$data['precio_unitario'];
        }

        if (isset($data['nombre_producto'])) {
            $detalle->nombre_producto = $data['nombre_producto'];
        }

        $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        $detalle->save();

        // Recalcular totales del pedido
        $pedido = Pedido::find($pedidoId);
        $total = $pedido->detalles()->sum('subtotal');
        $pedido->subtotal = $total;
        $pedido->total = $total;
        $pedido->save();

        return $detalle;
    }

    public function deleteDetalle(int $pedidoId, int $detalleId): bool
    {
        $detalle = DetallePedido::where('id', $detalleId)
            ->where('pedido_id', $pedidoId)
            ->firstOrFail();
        $detalle->delete();

        // Recalcular totales del pedido
        $pedido = Pedido::find($pedidoId);
        $total = $pedido->detalles()->sum('subtotal');
        $pedido->subtotal = $total;
        $pedido->total = $total;
        $pedido->save();

        return true;
    }
}
