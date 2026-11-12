<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use Core\Response;
use App\Models\Service;
use App\Integrations\RssClient;

class HomeController extends BaseController
{
    private Service $serviceModel;
    private RssClient $rssClient;

    public function __construct(
        ?Request $request = null,
        ?Response $response = null,
        ?Service $serviceModel = null,
        ?RssClient $rssClient = null
    ) {
        parent::__construct($request, $response);
        $this->serviceModel = $serviceModel ?? new Service();
        $this->rssClient = $rssClient ?? new RssClient();
    }

    /**
     * Landing homepage with hero featured tour, categories, experiences and travel news RSS.
     */
    public function index(): void
    {
        $bannerServices = $this->serviceModel->getBannerServices(4);
        $heroService = !empty($bannerServices) ? $bannerServices[0] : $this->serviceModel->getHeroFeatured();
        $categories = $this->serviceModel->getCategories();
        $services = $this->serviceModel->getAllApproved();

        // Fetch curated travel news from VnExpress via XML RSS parser
        $travelNews = $this->rssClient->getFeed(6);

        $this->view('home/index', [
            'title' => 'OnlineTraveller — Kết Nối Dân Bản Địa & Khách Du Lịch Từ Xa',
            'heroService' => $heroService,
            'bannerServices' => $bannerServices,
            'categories' => $categories,
            'services' => $services,
            'travelNews' => $travelNews,
        ]);
    }

    /**
     * API JSON Endpoint: Return parsed RSS travel news items (GET /api/rss/news).
     */
    public function news(Request $request): void
    {
        $limit = (int)($request->query('limit', 6));
        if ($limit < 1 || $limit > 20) {
            $limit = 6;
        }

        $news = $this->rssClient->getFeed($limit);

        $this->json([
            'success' => true,
            'total' => count($news),
            'data' => $news,
        ]);
    }
}

if (!class_exists('HomeController', false)) {
    class_alias(HomeController::class, 'HomeController');
}
