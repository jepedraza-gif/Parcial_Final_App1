<?php

use App\Presentation\Repositories\MesaRepository;
use App\Presentation\Repositories\ReservaRepository;
use Slim\App;

return function (App $app) {
    // Mesas
    $app->get('/mesas', [MesaRepository::class, 'all']);
    $app->post('/mesas', [MesaRepository::class, 'create']);
    $app->put('/mesas/{id}', [MesaRepository::class, 'update']);
    $app->patch('/mesas/{id}/estado', [MesaRepository::class, 'cambiarEstado']);
    $app->delete('/mesas/{id}', [MesaRepository::class, 'delete']);

    // Reservas
    $app->get('/reservas', [ReservaRepository::class, 'all']);
    $app->get('/reservas/fecha/{fecha}', [ReservaRepository::class, 'buscarPorFecha']);
    $app->get('/reservas/cliente/{cliente}', [ReservaRepository::class, 'buscarPorCliente']);
    $app->get('/reservas/estado/{estado}', [ReservaRepository::class, 'buscarPorEstado']);
    $app->post('/reservas', [ReservaRepository::class, 'create']);
    $app->put('/reservas/{id}', [ReservaRepository::class, 'update']);
    $app->delete('/reservas/{id}', [ReservaRepository::class, 'cancelar']);
};
