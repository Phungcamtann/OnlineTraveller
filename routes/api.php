<?php

declare(strict_types=1);

use Core\Router;
use Core\Request;
use Core\Response;
use Core\Database;
use App\Models\Service;

return function (Router $router): void {
    // 1. HEALTH CHECK & ARCHITECTURE STATUS
    $router->get('/api/health', function (Request $request) {
        $db = Database::getInstance();
        $tables = $db->fetchAll("SHOW TABLES;");
        $procedures = $db->fetchAll("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = 'PROCEDURE'");

        (new Response())->json([
            'status' => 'ok',
            'version' => '2.2',
            'app' => 'OnlineTraveller',
            'tables_count' => count($tables),
            'procedures_count' => count($procedures),
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    });

    // 2. 3D VIRTUAL TOUR STOPS (AJAX for Three.js Z-Scroll)
    $router->get('/api/services/{id}/stops', function (Request $request, string $id) {
        $serviceId = (int)$id;
        $stops = (new Service())->getStops($serviceId);

        (new Response())->json([
            'service_id' => $serviceId,
            'total_stops' => count($stops),
            'stops' => $stops,
        ]);
    });
};
