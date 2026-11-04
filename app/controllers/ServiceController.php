<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Service;

class ServiceController extends BaseController
{
    private Service $serviceModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->serviceModel = new Service();
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
     * Detail page of an experience (including 3D Three.js stops and Google Maps).
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

        $this->view('services/show', [
            'title' => $service['title'] . ' — OnlineTraveller',
            'service' => $service,
            'stops' => $stops,
        ]);
    }
}

if (!class_exists('ServiceController', false)) {
    class_alias(ServiceController::class, 'ServiceController');
}
