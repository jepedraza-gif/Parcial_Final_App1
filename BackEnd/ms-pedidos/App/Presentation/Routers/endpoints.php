<?php

use App\Presentation\Repositories\PedidoRepository;
use Slim\App;

return function (App $app) {
    // Pedidos
    $app->get('/pedidos', [PedidoRepository::class, 'all']);
    $app->get('/pedidos/estado/{estado}', [PedidoRepository::class, 'buscarPorEstado']);
    $app->get('/pedidos/{id}/detalles', [PedidoRepository::class, 'findWithDetails']);
    $app->post('/pedidos', [PedidoRepository::class, 'create']);
    $app->put('/pedidos/{id}', [PedidoRepository::class, 'update']);
    $app->delete('/pedidos/{id}', [PedidoRepository::class, 'delete']);

    // Detalles de pedido
    $app->post('/pedidos/{id}/detalles', [PedidoRepository::class, 'addDetalle']);
    $app->put('/pedidos/{id}/detalles/{detalle_id}', [PedidoRepository::class, 'updateDetalle']);
    $app->delete('/pedidos/{id}/detalles/{detalle_id}', [PedidoRepository::class, 'deleteDetalle']);
};
