<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use App\Models\Service;

class HomeController extends BaseController
{
    public function index(): void
    {
        $serviceModel = new Service();

        $heroService = $serviceModel->getHeroFeatured();
        $categories = $serviceModel->getCategories();
        $services = $serviceModel->getAllApproved();

        $this->view('home/index', [
            'title' => 'OnlineTraveller — Kết Nối Dân Bản Địa & Khách Du Lịch Từ Xa',
            'heroService' => $heroService,
            'categories' => $categories,
            'services' => $services,
        ]);
    }
}

if (!class_exists('HomeController', false)) {
    class_alias(HomeController::class, 'HomeController');
}
