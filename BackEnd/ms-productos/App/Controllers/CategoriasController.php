<?php

namespace App\Controllers;

use App\Models\Categoria;
use Exception;

class CategoriasController
{
    public function all(): array
    {
        return Categoria::all()->toArray();
    }

    public function create(array $data): Categoria
    {
        $existe = Categoria::where('nombre', $data['nombre'])->first();
        if ($existe) {
            throw new Exception('Ya existe una categoría con ese nombre', 1);
        }

        $categoria = new Categoria();
        $categoria->nombre = $data['nombre'];
        $categoria->descripcion = $data['descripcion'] ?? null;
        $categoria->save();

        return $categoria;
    }

    public function update(int $id, array $data): Categoria
    {
        $categoria = Categoria::findOrFail($id);

        if (isset($data['nombre'])) {
            $duplicada = Categoria::where('nombre', $data['nombre'])
                ->where('id', '!=', $id)
                ->first();
            if ($duplicada) {
                throw new Exception('Ya existe una categoría con ese nombre', 1);
            }
            $categoria->nombre = $data['nombre'];
        }

        if (isset($data['descripcion'])) {
            $categoria->descripcion = $data['descripcion'];
        }

        $categoria->save();
        return $categoria;
    }

    public function delete(int $id): bool
    {
        $categoria = Categoria::findOrFail($id);
        return $categoria->delete();
    }
}
