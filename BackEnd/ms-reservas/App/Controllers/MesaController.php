<?php

namespace App\Controllers;

use App\Models\Mesa;
use Exception;

class MesasController
{
    public function all(): array
    {
        return Mesa::all()->toArray();
    }

    public function create(array $data): Mesa
    {
        $existe = Mesa::where('numero', $data['numero'])->first();
        if ($existe) {
            throw new Exception('Ya existe una mesa con ese número', 1);
        }

        if ((int)$data['capacidad'] <= 0) {
            throw new Exception('La capacidad debe ser mayor a cero', 1);
        }

        $mesa = new Mesa();
        $mesa->numero = $data['numero'];
        $mesa->capacidad = (int)$data['capacidad'];
        $mesa->estado = $data['estado'] ?? 'disponible';
        $mesa->save();

        return $mesa;
    }

    public function update(int $id, array $data): Mesa
    {
        $mesa = Mesa::findOrFail($id);

        if (isset($data['capacidad']) && (int)$data['capacidad'] <= 0) {
            throw new Exception('La capacidad debe ser mayor a cero', 1);
        }

        if (isset($data['numero'])) {
            $duplicada = Mesa::where('numero', $data['numero'])
                ->where('id', '!=', $id)
                ->first();
            if ($duplicada) {
                throw new Exception('Ya existe una mesa con ese número', 1);
            }
            $mesa->numero = $data['numero'];
        }

        if (isset($data['capacidad'])) {
            $mesa->capacidad = (int)$data['capacidad'];
        }

        if (isset($data['estado'])) {
            $mesa->estado = $data['estado'];
        }

        $mesa->save();
        return $mesa;
    }

    public function cambiarEstado(int $id, string $estado): Mesa
    {
        $mesa = Mesa::findOrFail($id);
        $estadosValidos = ['disponible', 'reservada', 'ocupada', 'fuera_servicio'];

        if (!in_array($estado, $estadosValidos)) {
            throw new Exception('Estado no válido', 1);
        }

        $mesa->estado = $estado;
        $mesa->save();
        return $mesa;
    }

    public function delete(int $id): bool
    {
        $mesa = Mesa::findOrFail($id);
        return $mesa->delete();
    }
}
