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
        $maxPrice = (int)$request->query('max_price', 0);
        $priceRange = (string)$request->query('price_range', '');

        $filterMinPrice = null;
        $filterMaxPrice = null;

        // Support range slider parameter (max_price)
        if ($maxPrice > 0) {
            if ($maxPrice < 5000000) {
                $filterMaxPrice = $maxPrice;
            }
        } elseif (!empty($priceRange)) {
            // Support legacy price_range select
            if ($priceRange === 'under_500') {
                $filterMaxPrice = 500000;
                $maxPrice = 500000;
            } elseif ($priceRange === '500_1000') {
                $filterMinPrice = 500000;
                $filterMaxPrice = 1000000;
                $maxPrice = 1000000;
            } elseif ($priceRange === 'over_1000') {
                $filterMinPrice = 1000000;
                $maxPrice = 5000000;
            }
        }

        $categories = $this->serviceModel->getCategories();
        $services = $this->serviceModel->getAllApproved(
            $categoryId ?: null,
            $keyword ?: null,
            24,
            $filterMaxPrice,
            $filterMinPrice
        );

        $this->view('services/index', [
            'title' => 'Khám Phá Trải Nghiệm Bản Địa — OnlineTraveller',
            'services' => $services,
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
            'keyword' => $keyword,
            'maxPrice' => $maxPrice > 0 ? $maxPrice : 5000000,
            'priceRange' => $priceRange,
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

        // Fetch tour reviews & rating stats (Phase 2)
        $reviewModel = new \App\Models\Review();
        $reviews = $reviewModel->getByService($serviceId);
        $reviewStats = $reviewModel->getServiceRatingStats($serviceId);

        $user = $this->user();
        $hasReviewed = $user ? $reviewModel->hasUserReviewed((int)$user['id'], $serviceId) : false;
        $isEligibleVerified = $user ? $reviewModel->hasCompletedBooking((int)$user['id'], $serviceId) : false;

        $this->view('services/show', [
            'title' => $service['title'] . ' — OnlineTraveller',
            'service' => $service,
            'stops' => $stops,
            'weather' => $weather,
            'discussions' => $discussions,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
            'user' => $user,
            'hasReviewed' => $hasReviewed,
            'isEligibleVerified' => $isEligibleVerified,
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
