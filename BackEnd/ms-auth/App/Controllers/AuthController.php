<?php

namespace App\Controllers;

use App\Models\Usuario;
use Exception;

class AuthController
{
    public function login(array $data): array
    {
        $usuario = Usuario::where('usuario', $data['usuario'])
            ->where('contrasena', $data['contrasena'])
            ->where('estado', 'activo')
            ->first();

        if (!$usuario) {
            throw new Exception('Credenciales incorrectas', 1);
        }

        $token = bin2hex(random_bytes(16));
        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        return [
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'usuario' => $usuario->usuario,
                'rol' => $usuario->rol
            ],
            'mensaje' => 'Login exitoso'
        ];
    }

    public function logout(string $token): array
    {
        $usuario = Usuario::where('token', $token)->first();

        if ($usuario) {
            $usuario->token = null;
            $usuario->sesion_activa = false;
            $usuario->save();
        }

        return ['mensaje' => 'Sesión cerrada'];
    }

    public function validarSesion(string $token): array
    {
        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->where('estado', 'activo')
            ->first();

        if (!$usuario) {
            throw new Exception('Sesión no válida o expirada', 1);
        }

        return [
            'valido' => true,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'usuario' => $usuario->usuario,
                'rol' => $usuario->rol
            ]
        ];
    }
}
