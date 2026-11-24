<?php

namespace App\Presentation\Repositories;

use App\Controllers\MesasController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MesaRepository
{
    public function all(Request $request, Response $response): Response
    {
        try {
            $controller = new MesasController();
            $mesas = $controller->all();
            $response->getBody()->write(json_encode($mesas));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new MesasController();
            $mesa = $controller->create($data);
            $response->getBody()->write(json_encode($mesa));
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
            $controller = new MesasController();
            $mesa = $controller->update($id, $data);
            $response->getBody()->write(json_encode($mesa));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus($ex->getCode() === 1 ? 400 : 404);
        }
    }

    public function cambiarEstado(Request $request, Response $response, $args): Response
    {
        try {
            $id = (int)$args['id'];
            $data = json_decode($request->getBody()->getContents(), true);
            $controller = new MesasController();
            $mesa = $controller->cambiarEstado($id, $data['estado']);
            $response->getBody()->write(json_encode($mesa));
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
            $controller = new MesasController();
            $controller->delete($id);
            $response->getBody()->write(json_encode(['mensaje' => 'Mesa eliminada']));
            return $response->withHeader("Content-Type", "application/json")->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(404);
        }
    }
}
