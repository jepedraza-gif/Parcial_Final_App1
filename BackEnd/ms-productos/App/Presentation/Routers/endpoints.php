<?php

use App\Presentation\Repositories\CategoriaRepository;
use App\Presentation\Repositories\ProductoRepository;
use Slim\App;

return function (App $app) {
    // Categorías
    $app->get('/categorias', [CategoriaRepository::class, 'all']);
    $app->post('/categorias', [CategoriaRepository::class, 'create']);
    $app->put('/categorias/{id}', [CategoriaRepository::class, 'update']);
    $app->delete('/categorias/{id}', [CategoriaRepository::class, 'delete']);

    // Productos
    $app->get('/productos', [ProductoRepository::class, 'all']);
    $app->get('/productos/categoria/{categoria_id}', [ProductoRepository::class, 'buscarPorCategoria']);
    $app->get('/productos/disponibles', [ProductoRepository::class, 'disponibles']);
    $app->post('/productos', [ProductoRepository::class, 'create']);
    $app->put('/productos/{id}', [ProductoRepository::class, 'update']);
    $app->delete('/productos/{id}', [ProductoRepository::class, 'delete']);
};
