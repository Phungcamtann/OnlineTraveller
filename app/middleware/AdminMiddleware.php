<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Core\Middleware;
use Core\Request;

class AdminMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $roleMiddleware = new RoleMiddleware('admin');
        return $roleMiddleware->handle($request, $next);
    }
}

if (!class_exists('AdminMiddleware', false)) {
    class_alias(AdminMiddleware::class, 'AdminMiddleware');
}
