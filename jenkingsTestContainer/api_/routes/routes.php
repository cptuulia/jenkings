<?php

/** @var Bramus\Router\Router $router */

$router->mount('/api', function () use ($router) {
// Define routes here
    $router->get('/test', App\Controllers\IndexController::class . '@test');
    $router->get('/', App\Controllers\IndexController::class . '@test');


    $router->get('/pdf', App\Controllers\PdfController::class . '@index');
    $router->get('/pdf/(\d+)', App\Controllers\PdfController::class . '@show');
    $router->post('/pdf', App\Controllers\PdfController::class . '@store');
    $router->put('/pdf/(\d+)', App\Controllers\PdfController::class . '@update');
    $router->delete('/pdf/(\d+)', App\Controllers\PdfController::class . '@destroy');

    $router->get('/admin/users', App\Controllers\AdminUsersController::class . '@index');
    $router->get('/admin/users/(\d+)', App\Controllers\AdminUsersController::class . '@show');
    $router->post('/admin/users/login', App\Controllers\AdminUsersController::class . '@login');
    $router->post('/admin/users/logout/(\d+)', App\Controllers\AdminUsersController::class . '@logout');
    $router->post('/admin/users', App\Controllers\AdminUsersController::class . '@store');
    $router->put('/admin/users', App\Controllers\AdminUsersController::class . '@update');
    $router->delete('/admin/users/(\d+)', App\Controllers\AdminUsersController::class . '@destroy');

});