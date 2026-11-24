<?php

namespace App\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Exception;

class ProductosController
{
    public function all(): array
    {
        return Producto::with('categoria')->get()->toArray();
    }

    public function create(array $data): Producto
    {
        $categoria = Categoria::find($data['categoria_id']);
        if (!$categoria) {
            throw new Exception('La categoría no existe', 1);
        }

        if ((float)$data['precio'] <= 0) {
            throw new Exception('El precio debe ser mayor a cero', 1);
        }

        $producto = new Producto();
        $producto->nombre = $data['nombre'];
        $producto->descripcion = $data['descripcion'] ?? null;
        $producto->precio = (float)$data['precio'];
        $producto->categoria_id = (int)$data['categoria_id'];
        $producto->disponible = isset($data['disponible']) ? (bool)$data['disponible'] : true;
        $producto->save();

        return $producto;
    }

    public function update(int $id, array $data): Producto
    {
        $producto = Producto::findOrFail($id);

        if (isset($data['categoria_id'])) {
            $categoria = Categoria::find($data['categoria_id']);
            if (!$categoria) {
                throw new Exception('La categoría no existe', 1);
            }
            $producto->categoria_id = (int)$data['categoria_id'];
        }

        if (isset($data['nombre'])) {
            $producto->nombre = $data['nombre'];
        }

        if (isset($data['descripcion'])) {
            $producto->descripcion = $data['descripcion'];
        }

        if (isset($data['precio'])) {
            if ((float)$data['precio'] <= 0) {
                throw new Exception('El precio debe ser mayor a cero', 1);
            }
            $producto->precio = (float)$data['precio'];
        }

        if (isset($data['disponible'])) {
            $producto->disponible = (bool)$data['disponible'];
        }

        $producto->save();
        return $producto;
    }

    public function delete(int $id): bool
    {
        $producto = Producto::findOrFail($id);
        return $producto->delete();
    }
}
