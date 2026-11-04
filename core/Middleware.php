<?php

declare(strict_types=1);

namespace Core;

use Closure;

/**
 * Base interface for HTTP middleware in OnlineTraveller.
 */
interface Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next Next handler in pipeline: function(Request $request): mixed
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed;
}

if (!interface_exists('Middleware', false)) {
    class_alias(Middleware::class, 'Middleware');
}
