<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Core\Middleware;
use Core\Request;

class HostMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $roleMiddleware = new RoleMiddleware('host', 'admin');
        return $roleMiddleware->handle($request, $next);
    }
}

if (!class_exists('HostMiddleware', false)) {
    class_alias(HostMiddleware::class, 'HostMiddleware');
}
