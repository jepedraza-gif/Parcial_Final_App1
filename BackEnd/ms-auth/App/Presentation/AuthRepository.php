<?php

namespace App\Presentation;

use App\Controllers\AuthController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthRepository
{
    public function login(Request $request, Response $response): Response
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);

            $controller = new AuthController();
            $result = $controller->login($data);

            $response->getBody()->write(json_encode($result));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(401);
        }
    }

    public function logout(Request $request, Response $response): Response
    {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            $token = str_replace('Bearer ', '', $authHeader);

            $controller = new AuthController();
            $result = $controller->logout($token);

            $response->getBody()->write(json_encode($result));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(400);
        }
    }

    public function validarSesion(Request $request, Response $response): Response
    {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            $token = str_replace('Bearer ', '', $authHeader);

            $controller = new AuthController();
            $result = $controller->validarSesion($token);

            $response->getBody()->write(json_encode($result));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(200);
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['error' => $ex->getMessage()]));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(401);
        }
    }
}
