<?php

namespace App\Presentation\Repositories;

use App\Controllers\PedidosController;
use App\Models\DetallePedido;
use App\Models\Pedido;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PedidoRepository
{
    public function all(Request $request, Response $response): Response
    {
        $pedidos = Pedido::with('detalles')->get();
        $resultado = [];
        foreach ($pedidos as $pedido) {
            $arr = $pedido->toArray();
            $arr['cantidad_total'] = $pedido->detalles->sum('cantidad');
            $resultado[] = $arr;
        }
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader("Content-Type", "application/json");
    }

    public function findWithDetails(Request $request, Response $response, $args): Response
    {
        try {
            $id = $args['id'];
            $pedido = Pedido::with('detalles')->findOrFail($id);
            $arr = $pedido->toArray();
            $arr['cantidad_total'] = $pedido->detalles->sum('cantidad');
            $response->getBody()->write(json_encode($arr));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(404);
        }
    }

    public function buscarPorEstado(Request $request, Response $response, $args): Response
    {
        try {
            $estado = $args['estado'];
            $pedidos = Pedido::with('detalles')->where('estado', $estado)->get();
            $resultado = [];
            foreach ($pedidos as $pedido) {
                $arr = $pedido->toArray();
                $arr['cantidad_total'] = $pedido->detalles->sum('cantidad');
                $resultado[] = $arr;
            }
            $response->getBody()->write(json_encode($resultado));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new PedidosController();
            $pedido = $controller->create($data);
            $arr = $pedido->toArray();
            $arr['cantidad_total'] = $pedido->cantidad_total ?? $pedido->detalles->sum('cantidad');
            $response->getBody()->write(json_encode($arr));
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
            $id = $args['id'];
            $controller = new PedidosController();
            $controller->delete($id);
            $response->getBody()->write(json_encode(['mensaje' => 'Pedido eliminado']));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(404);
        }
    }

    public function addDetalle(Request $request, Response $response, $args): Response
    {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new PedidosController();
            $detalle = $controller->addDetalle($id, $data);
            $response->getBody()->write(json_encode($detalle));
            return $response->withHeader("Content-Type", "application/json")->withStatus(201);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus($ex->getCode() === 1 ? 400 : 404);
        }
    }

    public function updateDetalle(Request $request, Response $response, $args): Response
    {
        try {
            $pedidoId = $args['id'];
            $detalleId = $args['detalle_id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new PedidosController();
            $detalle = $controller->updateDetalle($pedidoId, $detalleId, $data);
            $response->getBody()->write(json_encode($detalle));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus($ex->getCode() === 1 ? 400 : 404);
        }
    }

    public function deleteDetalle(Request $request, Response $response, $args): Response
    {
        try {
            $pedidoId = $args['id'];
            $detalleId = $args['detalle_id'];
            $controller = new PedidosController();
            $controller->deleteDetalle($pedidoId, $detalleId);
            $response->getBody()->write(json_encode(['mensaje' => 'Detalle eliminado']));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus($ex->getCode() === 1 ? 400 : 404);
        }
    }
}
