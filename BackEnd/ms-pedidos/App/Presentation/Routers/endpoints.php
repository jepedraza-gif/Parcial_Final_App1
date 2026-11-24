<?php

use App\Presentation\Repositories\PedidoRepository;
use Slim\App;

return function (App $app) {
    // Pedidos
    $app->get('/pedidos', [PedidoRepository::class, 'all']);
    $app->get('/pedidos/{id}/detalles', [PedidoRepository::class, 'findWithDetails']);
    $app->post('/pedidos', [PedidoRepository::class, 'create']);
    $app->put('/pedidos/{id}', [PedidoRepository::class, 'update']);
    $app->delete('/pedidos/{id}', [PedidoRepository::class, 'delete']);
};
