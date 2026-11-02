<?php

declare(strict_types=1);

/**
 * Security and sanitization helper: CSRF tokens, HTML escaping and safe redirects.
 */
class SecurityHelper
{
    /**
     * Escape string for safe HTML output.
     */
    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Generate or retrieve current CSRF token from session.
     */
    public static function csrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify submitted CSRF token.
     */
    public static function verifyCsrfToken(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
