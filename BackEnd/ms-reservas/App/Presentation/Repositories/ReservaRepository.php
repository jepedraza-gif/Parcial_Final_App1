<?php

namespace App\Presentation\Repositories;

use App\Controllers\ReservasController;
use App\Models\Reserva;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReservaRepository
{
    public function all(Request $request, Response $response): Response
    {
        $reservas = Reserva::all();
        $response->getBody()->write($reservas->toJson());
        return $response->withHeader("Content-Type", "application/json");
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new ReservasController();
            $reserva = $controller->create($data);
            $response->getBody()->write($reserva->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(201);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(400);
        }
    }

    public function update(Request $request, Response $response, $args): Response
    {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new ReservasController();
            $reserva = $controller->update($id, $data);
            $response->getBody()->write($reserva->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus($ex->getCode() === 1 ? 400 : 404);
        }
    }

    public function cancelar(Request $request, Response $response, $args): Response
    {
        try {
            $id = $args['id'];
            $controller = new ReservasController();
            $reserva = $controller->cancelar($id);
            $response->getBody()->write($reserva->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus($ex->getCode() === 1 ? 400 : 404);
        }
    }

    public function buscarPorFecha(Request $request, Response $response, $args): Response
    {
        try {
            $fecha = $args['fecha'];
            $reservas = Reserva::where('fecha', $fecha)->get();
            $response->getBody()->write($reservas->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }

    public function buscarPorCliente(Request $request, Response $response, $args): Response
    {
        try {
            $cliente = $args['cliente'];
            $reservas = Reserva::where('nombre_cliente', 'like', '%' . $cliente . '%')->get();
            $response->getBody()->write($reservas->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }

    public function buscarPorEstado(Request $request, Response $response, $args): Response
    {
        try {
            $estado = $args['estado'];
            $reservas = Reserva::where('estado', $estado)->get();
            $response->getBody()->write($reservas->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }
}
