<?php

namespace App\Presentation\Repositories;

use App\Controllers\ProductosController;
use App\Models\Producto;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductoRepository
{
    public function all(Request $request, Response $response): Response
    {
        $productos = Producto::with('categoria')->get();
        $response->getBody()->write($productos->toJson());
        return $response->withHeader("Content-Type", "application/json");
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            $controller = new ProductosController();
            $producto = $controller->create($data);
            $response->getBody()->write($producto->toJson());
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
            $controller = new ProductosController();
            $producto = $controller->update($id, $data);
            $response->getBody()->write($producto->toJson());
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
            $controller = new ProductosController();
            $controller->delete($id);
            $response->getBody()->write(json_encode(['mensaje' => 'Producto eliminado']));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(404);
        }
    }

    public function buscarPorCategoria(Request $request, Response $response, $args): Response
    {
        try {
            $categoriaId = $args['categoria_id'];
            $productos = Producto::with('categoria')->where('categoria_id', $categoriaId)->get();
            $response->getBody()->write($productos->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }

    public function disponibles(Request $request, Response $response): Response
    {
        try {
            $productos = Producto::with('categoria')->where('disponible', true)->get();
            $response->getBody()->write($productos->toJson());
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }
}
