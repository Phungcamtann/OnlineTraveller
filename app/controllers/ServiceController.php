<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use Core\Response;
use App\Models\Service;
use App\Integrations\OpenWeatherClient;

class ServiceController extends BaseController
{
    private Service $serviceModel;
    private OpenWeatherClient $weatherClient;

    public function __construct(
        ?Request $request = null,
        ?Response $response = null,
        ?Service $serviceModel = null,
        ?OpenWeatherClient $weatherClient = null
    ) {
        parent::__construct($request, $response);
        $this->serviceModel = $serviceModel ?? new Service();
        $this->weatherClient = $weatherClient ?? new OpenWeatherClient();
    }

    /**
     * List all approved experiences with optional filters.
     */
    public function index(Request $request): void
    {
        $categoryId = (int)$request->query('category_id', 0);
        $keyword = (string)$request->query('location', '');
        
        $categories = $this->serviceModel->getCategories();
        $services = $this->serviceModel->getAllApproved($categoryId ?: null, $keyword ?: null, 24);

        $this->view('services/index', [
            'title' => 'Khám Phá Trải Nghiệm Bản Địa — OnlineTraveller',
            'services' => $services,
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
            'keyword' => $keyword,
        ]);
    }

    /**
     * Detail page of an experience (including 3D Three.js stops, Google Maps and destination weather).
     */
    public function show(Request $request, string $id): void
    {
        $serviceId = (int)$id;
        $service = $this->serviceModel->getById($serviceId);

        if (!$service) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy gói trải nghiệm']);
            return;
        }

        $stops = $this->serviceModel->getStops($serviceId);

        // Fetch destination weather via OpenWeatherClient (with cache & intelligent regional fallback)
        $weather = $this->weatherClient->getWeatherForService($service);

        // Fetch live community discussions & replies (TV4-05)
        $discussions = (new \App\Models\Discussion())->getDiscussionsByService($serviceId);

        $this->view('services/show', [
            'title' => $service['title'] . ' — OnlineTraveller',
            'service' => $service,
            'stops' => $stops,
            'weather' => $weather,
            'discussions' => $discussions,
        ]);
    }

    /**
     * JSON Webservice Endpoint: Return real-time destination weather forecast (GET /api/weather).
     */
    public function weather(Request $request): void
    {
        $location = (string)($request->query('location') ?? '');
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        if ($lat !== null && $lon !== null && is_numeric($lat) && is_numeric($lon)) {
            $data = $this->weatherClient->getWeatherByCoords((float)$lat, (float)$lon, $location);
        } else {
            $data = $this->weatherClient->getWeatherByCity($location !== '' ? $location : 'Đà Lạt');
        }

        $this->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

if (!class_exists('ServiceController', false)) {
    class_alias(ServiceController::class, 'ServiceController');
}
