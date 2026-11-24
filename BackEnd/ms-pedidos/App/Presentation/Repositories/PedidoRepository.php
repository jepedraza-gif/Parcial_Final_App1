<?php

namespace App\Presentation\Repositories;

use App\Controllers\PedidosController;
use App\Models\Pedido;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PedidoRepository
{
    public function all(Request $request, Response $response): Response
    {
        try {
            $controller = new PedidosController();
            $pedidos = $controller->all();
            $response->getBody()->write(json_encode($pedidos));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }

    public function findWithDetails(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int)$args['id'];
            $pedido = Pedido::with('detalles')->findOrFail($id);
            $response->getBody()->write(json_encode($pedido));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(404);
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new PedidosController();
            $pedido = $controller->create($data);
            $response->getBody()->write(json_encode($pedido));
            return $response->withHeader("Content-Type", "application/json")->withStatus(201);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(400);
        }
    }

    public function update(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int)$args['id'];
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new PedidosController();
            $pedido = $controller->update($id, $data);
            $response->getBody()->write(json_encode($pedido));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus($ex->getCode() === 1 ? 400 : 404);
        }
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int)$args['id'];
            $controller = new PedidosController();
            $controller->delete($id);
            $response->getBody()->write(json_encode(['mensaje' => 'Pedido eliminado']));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(404);
        }
    }
}
