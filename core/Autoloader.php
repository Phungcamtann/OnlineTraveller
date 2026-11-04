<?php

declare(strict_types=1);

namespace Core;

/**
 * Autoloader for OnlineTraveller OOP MVC application.
 * Maps both namespaced classes (Core\*, App\*) and top-level class names to corresponding directory files.
 */
class Autoloader
{
    private static string $baseDir = '';

    public static function register(string $baseDir): void
    {
        self::$baseDir = rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR;

        spl_autoload_register([self::class, 'loadClass']);
    }

    public static function loadClass(string $className): bool
    {
        // 1. Try PSR-4 like namespace mapping
        $file = self::resolveNamespace($className);
        if ($file !== null && is_file($file)) {
            require_once $file;
            return true;
        }

        // 2. Try global / fallback resolution by class naming conventions
        $file = self::resolveFallback($className);
        if ($file !== null && is_file($file)) {
            require_once $file;
            return true;
        }

        return false;
    }

    private static function resolveNamespace(string $className): ?string
    {
        $normalized = ltrim($className, '\\');

        if (str_starts_with($normalized, 'Core\\')) {
            $subPath = substr($normalized, 5);
            return self::$baseDir . 'core' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $subPath) . '.php';
        }

        if (str_starts_with($normalized, 'App\\')) {
            $subPath = substr($normalized, 4);
            $parts = explode('\\', $subPath);

            // Convert directory names to lowercase (e.g., App\Controllers\HomeController -> app/controllers/HomeController.php)
            if (count($parts) > 1) {
                $dirParts = array_slice($parts, 0, -1);
                $filePart = end($parts);
                $dir = implode(DIRECTORY_SEPARATOR, array_map('strtolower', $dirParts));
                return self::$baseDir . 'app' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $filePart . '.php';
            }

            return self::$baseDir . 'app' . DIRECTORY_SEPARATOR . $subPath . '.php';
        }

        return null;
    }

    private static function resolveFallback(string $className): ?string
    {
        // Strip any namespace
        $parts = explode('\\', $className);
        $shortName = end($parts);

        // Core classes
        $coreFile = self::$baseDir . 'core' . DIRECTORY_SEPARATOR . $shortName . '.php';
        if (is_file($coreFile)) {
            return $coreFile;
        }

        // Controllers
        if (str_ends_with($shortName, 'Controller')) {
            $file = self::$baseDir . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $shortName . '.php';
            if (is_file($file)) {
                return $file;
            }
        }

        // Services
        if (str_ends_with($shortName, 'Service')) {
            $file = self::$baseDir . 'app' . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . $shortName . '.php';
            if (is_file($file)) {
                return $file;
            }
        }

        // Middleware
        if (str_ends_with($shortName, 'Middleware')) {
            $file = self::$baseDir . 'app' . DIRECTORY_SEPARATOR . 'middleware' . DIRECTORY_SEPARATOR . $shortName . '.php';
            if (is_file($file)) {
                return $file;
            }
        }

        // Helpers
        if (str_ends_with($shortName, 'Helper')) {
            $file = self::$baseDir . 'app' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . $shortName . '.php';
            if (is_file($file)) {
                return $file;
            }
        }

        // Integrations / Clients
        if (str_ends_with($shortName, 'Client') || $shortName === 'RssReader') {
            $file = self::$baseDir . 'app' . DIRECTORY_SEPARATOR . 'integrations' . DIRECTORY_SEPARATOR . $shortName . '.php';
            if (is_file($file)) {
                return $file;
            }
        }

        // Models (fallback)
        $modelFile = self::$baseDir . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $shortName . '.php';
        if (is_file($modelFile)) {
            return $modelFile;
        }

        return null;
    }
}

// Global class alias so both \Core\Autoloader and Autoloader work seamlessly
if (!class_exists('Autoloader', false)) {
    class_alias(Autoloader::class, 'Autoloader');
}
