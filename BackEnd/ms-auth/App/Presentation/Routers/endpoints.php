<?php

use App\Presentation\Repositories\AuthRepository;
use Slim\App;

return function (App $app) {
    $app->post('/login', [AuthRepository::class, 'login']);
    $app->post('/logout', [AuthRepository::class, 'logout']);
    $app->get('/validar-sesion', [AuthRepository::class, 'validarSesion']);
};
