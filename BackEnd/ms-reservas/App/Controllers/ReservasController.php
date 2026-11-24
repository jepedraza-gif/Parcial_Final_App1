<?php

namespace App\Controllers;

use App\Models\Mesa;
use App\Models\Reserva;
use Exception;

class ReservasController
{
    public function all(): array
    {
        return Reserva::with('mesa')->get()->toArray();
    }

    public function create(array $data): Reserva
    {
        $fecha = new \DateTime($data['fecha']);
        $hoy = new \DateTime('today');
        if ($fecha < $hoy) {
            throw new Exception('No se permite reservar en fechas pasadas', 1);
        }

        $mesa = Mesa::find($data['mesa_id']);
        if (!$mesa) {
            throw new Exception('La mesa no existe', 1);
        }

        if ($mesa->estado === 'fuera_servicio') {
            throw new Exception('No se puede reservar una mesa fuera de servicio', 1);
        }

        if ((int)$data['cantidad_personas'] > (int)$mesa->capacidad) {
            throw new Exception('La cantidad de personas excede la capacidad de la mesa', 1);
        }

        $horaInicio = new \DateTime($data['hora']);
        $horaFin = clone $horaInicio;
        $horaFin->modify('+1 hour');

        $conflicto = Reserva::where('mesa_id', $data['mesa_id'])
            ->where('fecha', $data['fecha'])
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->get()
            ->filter(function ($r) use ($horaInicio, $horaFin) {
                $rInicio = new \DateTime($r->hora);
                $rFin = clone $rInicio;
                $rFin->modify('+1 hour');
                return $horaInicio < $rFin && $rInicio < $horaFin;
            });

        if ($conflicto->count() > 0) {
            throw new Exception('Ya existe una reserva para esta mesa en ese horario', 1);
        }

        $reserva = new Reserva();
        $reserva->nombre_cliente = $data['nombre_cliente'];
        $reserva->telefono_cliente = $data['telefono_cliente'];
        $reserva->cantidad_personas = (int)$data['cantidad_personas'];
        $reserva->fecha = $data['fecha'];
        $reserva->hora = $data['hora'];
        $reserva->observaciones = $data['observaciones'] ?? null;
        $reserva->estado = 'pendiente';
        $reserva->mesa_id = (int)$data['mesa_id'];
        $reserva->save();

        return $reserva;
    }

    public function update(int $id, array $data): Reserva
    {
        $reserva = Reserva::findOrFail($id);
        $mesaId = $data['mesa_id'] ?? $reserva->mesa_id;
        $mesa = Mesa::find($mesaId);

        if (!$mesa) {
            throw new Exception('La mesa no existe', 1);
        }

        $fecha = $data['fecha'] ?? $reserva->fecha;
        $hora = $data['hora'] ?? $reserva->hora;

        if ($mesa->estado === 'fuera_servicio') {
            throw new Exception('No se puede usar una mesa fuera de servicio', 1);
        }

        if ((int)($data['cantidad_personas'] ?? $reserva->cantidad_personas) > (int)$mesa->capacidad) {
            throw new Exception('La cantidad de personas excede la capacidad de la mesa', 1);
        }

        $horaInicio = new \DateTime($hora);
        $horaFin = clone $horaInicio;
        $horaFin->modify('+1 hour');

        $conflicto = Reserva::where('mesa_id', $mesaId)
            ->where('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('id', '!=', $id)
            ->get()
            ->filter(function ($r) use ($horaInicio, $horaFin) {
                $rInicio = new \DateTime($r->hora);
                $rFin = clone $rInicio;
                $rFin->modify('+1 hour');
                return $horaInicio < $rFin && $rInicio < $horaFin;
            });

        if ($conflicto->count() > 0) {
            throw new Exception('Ya existe una reserva para esta mesa en ese horario', 1);
        }

        if (isset($data['nombre_cliente'])) $reserva->nombre_cliente = $data['nombre_cliente'];
        if (isset($data['telefono_cliente'])) $reserva->telefono_cliente = $data['telefono_cliente'];
        if (isset($data['cantidad_personas'])) $reserva->cantidad_personas = (int)$data['cantidad_personas'];
        if (isset($data['fecha'])) $reserva->fecha = $data['fecha'];
        if (isset($data['hora'])) $reserva->hora = $data['hora'];
        if (isset($data['observaciones'])) $reserva->observaciones = $data['observaciones'];
        if (isset($data['mesa_id'])) $reserva->mesa_id = (int)$data['mesa_id'];

        $reserva->save();
        return $reserva;
    }

    public function cancelar(int $id): Reserva
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->estado = 'cancelada';
        $reserva->save();
        return $reserva;
    }
}
