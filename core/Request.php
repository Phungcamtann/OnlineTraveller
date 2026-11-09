<?php

declare(strict_types=1);

namespace Core;

/**
 * Encapsulates the current HTTP request.
 * Normalizes query parameters, POST data, JSON payloads, uploaded files, and route parameters.
 */
class Request
{
    private string $method;
    private string $uri;
    private string $path;
    private array $queryParams;
    private array $postParams;
    private ?array $jsonParams = null;
    private array $server;
    private array $files;
    private array $routeParams = [];

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->queryParams = $_GET;
        $this->postParams = $_POST;
        $this->files = $_FILES;

        $rawMethod = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($rawMethod === 'POST' && isset($this->postParams['_method'])) {
            $this->method = strtoupper((string)$this->postParams['_method']);
        } else {
            $this->method = $rawMethod;
        }

        $this->uri = $this->server['REQUEST_URI'] ?? '/';
        $parsedPath = parse_url($this->uri, PHP_URL_PATH) ?? '/';

        // Strip script base directory if running from subfolder (e.g., /online-traveller/public)
        $scriptName = $this->server['SCRIPT_NAME'] ?? '';
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        if ($scriptDir !== '/' && str_starts_with($parsedPath, $scriptDir)) {
            $parsedPath = substr($parsedPath, strlen($scriptDir));
        }

        $this->path = '/' . trim($parsedPath, '/');
        if ($this->path === '') {
            $this->path = '/';
        }

        // Parse JSON body if Content-Type is application/json
        $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $rawInput = file_get_contents('php://input');
            if ($rawInput !== false && $rawInput !== '') {
                $decoded = json_decode($rawInput, true);
                if (is_array($decoded)) {
                    $this->jsonParams = $decoded;
                }
            }
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }

    public function isAjax(): bool
    {
        if (isset($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        $accept = $this->server['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json');
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->queryParams;
        }
        return $this->queryParams[$key] ?? $default;
    }

    public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->postParams;
        }
        return $this->postParams[$key] ?? $default;
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($this->jsonParams === null) {
            return $default;
        }
        if ($key === null) {
            return $this->jsonParams;
        }
        return $this->jsonParams[$key] ?? $default;
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return array_merge($this->queryParams, $this->postParams, $this->jsonParams ?? [], $this->routeParams);
        }

        if (array_key_exists($key, $this->routeParams)) {
            return $this->routeParams[$key];
        }
        if ($this->jsonParams !== null && array_key_exists($key, $this->jsonParams)) {
            return $this->jsonParams[$key];
        }
        if (array_key_exists($key, $this->postParams)) {
            return $this->postParams[$key];
        }
        if (array_key_exists($key, $this->queryParams)) {
            return $this->queryParams[$key];
        }

        return $default;
    }

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $name, ?string $default = null): ?string
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$serverKey] ?? $default;
    }

    public function csrfToken(): ?string
    {
        $token = $this->post('_csrf_token')
            ?? $this->post('csrf_token')
            ?? $this->json('_csrf_token')
            ?? $this->json('csrf_token')
            ?? $this->header('X-CSRF-TOKEN')
            ?? $this->header('X-XSRF-TOKEN');

        return is_string($token) && trim($token) !== '' ? trim($token) : null;
    }
}

if (!class_exists('Request', false)) {
    class_alias(Request::class, 'Request');
}
