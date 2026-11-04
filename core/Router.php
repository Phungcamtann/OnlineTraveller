<?php

declare(strict_types=1);

namespace Core;

use Closure;
use RuntimeException;

/**
 * HTTP Router for OnlineTraveller.
 * Handles route registration, parameterized matching, middleware execution,
 * and dispatching to controllers or closures.
 */
class Router
{
    private array $routes = [];
    private ?Closure $notFoundHandler = null;

    public function get(string $path, array|Closure|string $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|Closure|string $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array|Closure|string $handler, array $middleware = []): self
    {
        return $this->add('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, array|Closure|string $handler, array $middleware = []): self
    {
        return $this->add('DELETE', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, array|Closure|string $handler, array $middleware = []): self
    {
        $normalizedMethod = strtoupper($method);
        $normalizedPath = '/' . trim($path, '/');
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }

        // Convert route pattern with {param} to regex
        // e.g. /services/{id} -> #^/services/(?P<id>[^/]+)$#
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $normalizedPath);
        $regex = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $normalizedMethod,
            'path' => $normalizedPath,
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => $middleware,
        ];

        return $this;
    }

    public function setNotFoundHandler(Closure $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(Request $request): mixed
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        $methodMatched = false;

        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                if ($route['method'] !== $method) {
                    $methodMatched = true;
                    continue;
                }

                // Extract named capture groups
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($value);
                    }
                }
                $request->setRouteParams($params);

                // Build middleware pipeline
                $pipeline = $this->buildPipeline($route['middleware'], function (Request $req) use ($route, $params) {
                    return $this->invokeHandler($route['handler'], $req, $params);
                });

                return $pipeline($request);
            }
        }

        if ($methodMatched) {
            http_response_code(405);
            header('Content-Type: text/plain; charset=utf-8');
            echo "405 Method Not Allowed";
            exit;
        }

        return $this->handleNotFound($request);
    }

    private function buildPipeline(array $middlewareList, Closure $destination): Closure
    {
        return array_reduce(
            array_reverse($middlewareList),
            function (Closure $next, mixed $middleware) {
                return function (Request $request) use ($next, $middleware) {
                    $instance = is_string($middleware) ? new $middleware() : $middleware;
                    if (is_object($instance) && method_exists($instance, 'handle')) {
                        return $instance->handle($request, $next);
                    }
                    if (is_callable($instance)) {
                        return $instance($request, $next);
                    }
                    throw new RuntimeException("Invalid middleware specified.");
                };
            },
            $destination
        );
    }

    private function invokeHandler(array|Closure|string $handler, Request $request, array $params): mixed
    {
        if ($handler instanceof Closure) {
            return $handler($request, ...array_values($params));
        }

        if (is_array($handler)) {
            [$class, $method] = $handler;
            if (is_string($class)) {
                $controller = new $class($request, new Response());
            } else {
                $controller = $class;
            }

            if (!method_exists($controller, $method)) {
                throw new RuntimeException("Method [{$method}] not found on controller [" . get_class($controller) . "].");
            }

            return $controller->$method($request, ...array_values($params));
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$className, $methodName] = explode('@', $handler);
            return $this->invokeHandler([$className, $methodName], $request, $params);
        }

        throw new RuntimeException("Invalid route handler provided.");
    }

    private function handleNotFound(Request $request): mixed
    {
        if ($this->notFoundHandler !== null) {
            return ($this->notFoundHandler)($request);
        }

        http_response_code(404);
        if ($request->isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Resource not found', 'path' => $request->getPath()]);
            exit;
        }

        $errorView = dirname(__DIR__) . '/app/views/errors/404.php';
        if (is_file($errorView)) {
            include $errorView;
            exit;
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo "404 Not Found: " . $request->getPath();
        exit;
    }
}

if (!class_exists('Router', false)) {
    class_alias(Router::class, 'Router');
}
