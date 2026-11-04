<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

/**
 * Base controller providing view rendering, JSON responses, redirects,
 * flash messaging, and authentication helper shortcuts.
 */
abstract class BaseController
{
    protected Request $request;
    protected Response $response;

    public function __construct(?Request $request = null, ?Response $response = null)
    {
        $this->request = $request ?? new Request();
        $this->response = $response ?? new Response();
    }

    /**
     * Render an HTML view within a layout template.
     */
    protected function view(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        // Extract data variables into local scope for the view
        extract($data, EXTR_SKIP);

        $projectRoot = dirname(__DIR__);
        $cleanPath = trim(str_replace(['.', '/'], DIRECTORY_SEPARATOR, $viewPath), DIRECTORY_SEPARATOR);
        $viewFile = $projectRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $cleanPath . '.php';

        if (!is_file($viewFile)) {
            // Also try direct path if ending in .php
            $viewFileAlt = $projectRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . ltrim($viewPath, '/\\');
            if (is_file($viewFileAlt)) {
                $viewFile = $viewFileAlt;
            } else {
                throw new RuntimeException("View file not found: [{$viewFile}]");
            }
        }

        // Buffer view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // If no layout is specified, return raw view output
        if ($layout === null) {
            $this->response->html($content);
            return;
        }

        $cleanLayout = trim(str_replace(['.', '/'], DIRECTORY_SEPARATOR, $layout), DIRECTORY_SEPARATOR);
        $layoutFile = $projectRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $cleanLayout . '.php';

        if (!is_file($layoutFile)) {
            throw new RuntimeException("Layout file not found: [{$layoutFile}]");
        }

        // Buffer full layout wrapping $content
        ob_start();
        include $layoutFile;
        $html = ob_get_clean();

        $this->response->html($html);
    }

    /**
     * Send a JSON response.
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        $this->response->json($data, $statusCode);
    }

    /**
     * Redirect to another URL or route path.
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        $this->response->redirect($url, $statusCode);
    }

    /**
     * Set a one-time flash message in session.
     */
    protected function flash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['flash'][$type][] = $message;
    }

    /**
     * Retrieve and clear flash messages.
     */
    protected function getFlash(?string $type = null): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if ($type !== null) {
            $messages = $_SESSION['flash'][$type] ?? [];
            unset($_SESSION['flash'][$type]);
            return $messages;
        }

        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }

    /**
     * Get currently authenticated user array or null.
     */
    protected function user(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check if visitor is authenticated.
     */
    protected function isLoggedIn(): bool
    {
        return $this->user() !== null;
    }
}

if (!class_exists('BaseController', false)) {
    class_alias(BaseController::class, 'BaseController');
}
