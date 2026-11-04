<?php

declare(strict_types=1);

use Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ServiceController;
use App\Controllers\AuthController;
use App\Controllers\HostController;
use App\Controllers\AdminController;
use App\Middleware\AuthMiddleware;
use App\Middleware\HostMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\CsrfMiddleware;

return function (Router $router): void {
    // 1. PUBLIC STOREFRONT (TV4 & TV2)
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/services', [ServiceController::class, 'index']);
    $router->get('/services/{id}', [ServiceController::class, 'show']);

    // 2. AUTHENTICATION & PROFILE (TV1)
    $router->get('/login', [AuthController::class, 'loginForm']);
    $router->post('/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);
    $router->get('/register', [AuthController::class, 'registerForm']);
    $router->post('/register', [AuthController::class, 'register'], [CsrfMiddleware::class]);
    $router->get('/logout', [AuthController::class, 'logout']);

    // 3. HOST DASHBOARD & TOURS (TV2)
    $router->get('/host', [HostController::class, 'dashboard'], [AuthMiddleware::class, HostMiddleware::class]);

    // 4. ADMIN DASHBOARD & MODERATION (TV1 & TV4)
    $router->get('/admin', [AdminController::class, 'dashboard'], [AuthMiddleware::class, AdminMiddleware::class]);
};
