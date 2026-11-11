<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use Core\Response;
use App\Services\TourService;
use App\Services\ScheduleService;
use App\Services\BookingService;
use App\Models\Service;
use App\Models\Booking;
use FormatHelper;
use Exception;

/**
 * Host Controller: manages the Local Host experience portal:
 * - Host dashboard with KPI metrics
 * - Tour services CRUD (pricing modes, homestay attachment, 3D stops)
 * - Schedule coordination
 * - Booking review, approval and 15-minute seat reservation
 */
class HostController extends BaseController
{
    private TourService $tourService;
    private ScheduleService $scheduleService;
    private Service $serviceModel;
    private BookingService $bookingService;
    private Booking $bookingModel;

    public function __construct(
        ?Request $request = null,
        ?Response $response = null,
        ?TourService $tourService = null,
        ?ScheduleService $scheduleService = null,
        ?Service $serviceModel = null,
        ?BookingService $bookingService = null,
        ?Booking $bookingModel = null
    ) {
        parent::__construct($request, $response);
        $this->tourService = $tourService ?? new TourService();
        $this->scheduleService = $scheduleService ?? new ScheduleService();
        $this->serviceModel = $serviceModel ?? new Service();
        $this->bookingService = $bookingService ?? new BookingService();
        $this->bookingModel = $bookingModel ?? new Booking();
    }

    /**
     * Host main operational dashboard.
     */
    public function dashboard(): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $hostId = (int)$user['id'];
        $services = $this->tourService->getHostTours($hostId);
        $schedules = $this->scheduleService->getHostSchedules($hostId);

        // Calculate metrics
        $homestaysCount = count(array_filter($services, fn($s) => !empty($s['homestay_name'])));
        $activeSchedules = array_filter($schedules, fn($sch) => $sch['status'] === 'open' && strtotime($sch['starts_at']) > time());
        $totalBookings = array_sum(array_column($services, 'bookings_count'));

        $this->view('host/dashboard', [
            'title' => 'Bảng Quản Lý Dân Bản Địa (Host) — OnlineTraveller',
            'user' => $user,
            'services' => $services,
            'schedules' => array_slice($schedules, 0, 5),
            'metrics' => [
                'total_services' => count($services),
                'homestays_count' => $homestaysCount,
                'active_schedules_count' => count($activeSchedules),
                'total_bookings' => $totalBookings,
            ],
        ]);
    }

    /**
     * List all tours managed by the host.
     */
    public function services(): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $hostId = (int)$user['id'];
        $services = $this->tourService->getHostTours($hostId);

        $this->view('host/services/index', [
            'title' => 'Danh Sách Gói Trải Nghiệm Bản Địa — Host',
            'user' => $user,
            'services' => $services,
        ]);
    }

    /**
     * Show form to create a new tour experience.
     */
    public function createService(): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $categories = $this->serviceModel->getCategories();
        $provinces = FormatHelper::getProvincesList();

        $this->view('host/services/create', [
            'title' => 'Đăng Ký Gói Trải Nghiệm Mới — Host',
            'user' => $user,
            'categories' => $categories,
            'provinces' => $provinces,
        ]);
    }

    /**
     * Handle submission of new tour experience.
     */
    public function storeService(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $hostId = (int)$user['id'];

        try {
            // Handle banner image upload if file provided
            $bannerUrl = trim((string)$request->post('banner_url', ''));
            if (!empty($_FILES['banner_file']['name'])) {
                $bannerUrl = $this->tourService->handleBannerUpload($_FILES['banner_file']);
            }

            // Location & Geocoding calculation
            $location = trim((string)$request->post('location', ''));
            $province = trim((string)$request->post('province', ''));
            if (!empty($province) && !str_contains($location, $province)) {
                $location = !empty($location) ? ($location . ', ' . $province) : $province;
            }

            $rawLat = $request->post('latitude');
            $rawLng = $request->post('longitude');
            $latitude = ($rawLat !== null && $rawLat !== '') ? (float)$rawLat : null;
            $longitude = ($rawLng !== null && $rawLng !== '') ? (float)$rawLng : null;

            if (empty($latitude) || empty($longitude)) {
                $geo = FormatHelper::lookupCoordinates($location . ' ' . $province);
                $latitude = $geo['latitude'];
                $longitude = $geo['longitude'];
            }

            // Main tour data
            $tourData = [
                'category_id' => (int)$request->post('category_id', 0),
                'title' => (string)$request->post('title', ''),
                'slug' => (string)$request->post('slug', ''),
                'description' => (string)$request->post('description', ''),
                'price_mode' => (string)$request->post('price_mode', 'fixed_choice'),
                'base_price' => (float)$request->post('base_price', 0),
                'min_price' => (float)$request->post('min_price', 0),
                'max_price' => (float)$request->post('max_price', 0),
                'location' => $location,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'duration_days' => max(1, (int)$request->post('duration_days', 1)),
                'banner_image' => $bannerUrl,
            ];

            // Accommodation data
            $accommodationData = null;
            if ($request->post('has_homestay') === '1' && !empty($request->post('homestay_name'))) {
                $accommodationData = [
                    'homestay_name' => (string)$request->post('homestay_name', ''),
                    'address' => (string)$request->post('homestay_address', $tourData['location']),
                    'room_type' => (string)$request->post('room_type', 'Phòng Tiêu Chuẩn'),
                    'nights' => max(1, (int)$request->post('nights', 1)),
                    'notes' => (string)$request->post('homestay_notes', ''),
                ];
            }

            // Stops data with direct file upload & fallback URL support
            $stops = [];
            $stopImages = (array)$request->post('stop_image_urls', []);
            $stopCaptions = (array)$request->post('stop_captions', []);
            $stopStories = (array)$request->post('stop_stories', []);
            $stopVideos = (array)$request->post('stop_videos', []);
            $uploadedStopFiles = $_FILES['stop_files'] ?? null;

            $maxStops = max(
                count($stopImages),
                count($stopCaptions),
                (isset($uploadedStopFiles['name']) && is_array($uploadedStopFiles['name'])) ? count($uploadedStopFiles['name']) : 0
            );

            for ($i = 0; $i < $maxStops; $i++) {
                $caption = trim((string)($stopCaptions[$i] ?? ''));
                $story = trim((string)($stopStories[$i] ?? ''));
                $video = !empty($stopVideos[$i]) ? trim((string)$stopVideos[$i]) : null;
                $imageUrl = trim((string)($stopImages[$i] ?? ''));

                // Handle direct file upload if present
                if (isset($uploadedStopFiles['name'][$i]) && !empty($uploadedStopFiles['name'][$i]) && ($uploadedStopFiles['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name' => $uploadedStopFiles['name'][$i],
                        'type' => $uploadedStopFiles['type'][$i] ?? 'image/jpeg',
                        'tmp_name' => $uploadedStopFiles['tmp_name'][$i],
                        'error' => $uploadedStopFiles['error'][$i],
                        'size' => $uploadedStopFiles['size'][$i],
                    ];
                    $imageUrl = $this->tourService->handleStopImageUpload($singleFile);
                }

                if (!empty($imageUrl)) {
                    $stops[] = [
                        'image_url' => $imageUrl,
                        'caption' => $caption !== '' ? $caption : ('Điểm dừng ' . (count($stops) + 1)),
                        'story_text' => $story,
                        'video_url' => $video,
                        'sort_order' => count($stops) + 1,
                    ];
                }
            }

            $created = $this->tourService->createTour($hostId, $tourData, $accommodationData, $stops);

            $this->flash('success', 'Đăng ký gói trải nghiệm "' . htmlspecialchars($created['title']) . '" thành công! Gói tour đang ở trạng thái chờ duyệt.');
            $this->redirect('/host/services');
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
            $this->redirect('/host/services/create');
        }
    }

    /**
     * Show form to edit an existing tour experience.
     */
    public function editService(Request $request, string $id = ''): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $hostId = (int)$user['id'];
        $tourId = (int)($id ?: $request->param('id', 0));

        try {
            $tour = $this->tourService->getTourDetailForHost($hostId, $tourId);
            $categories = $this->serviceModel->getCategories();
            $provinces = FormatHelper::getProvincesList();

            $this->view('host/services/edit', [
                'title' => 'Chỉnh Sửa Gói Trải Nghiệm — Host',
                'user' => $user,
                'tour' => $tour,
                'categories' => $categories,
                'provinces' => $provinces,
            ]);
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
            $this->redirect('/host/services');
        }
    }

    /**
     * Handle update of tour experience.
     */
    public function updateService(Request $request, string $id = ''): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $hostId = (int)$user['id'];
        $tourId = (int)($id ?: $request->param('id', 0));

        try {
            // Handle banner image upload if file provided
            $bannerUrl = trim((string)$request->post('banner_url', ''));
            if (!empty($_FILES['banner_file']['name'])) {
                $bannerUrl = $this->tourService->handleBannerUpload($_FILES['banner_file']);
            }

            // Location & Geocoding calculation
            $location = trim((string)$request->post('location', ''));
            $province = trim((string)$request->post('province', ''));
            if (!empty($province) && !str_contains($location, $province)) {
                $location = !empty($location) ? ($location . ', ' . $province) : $province;
            }

            $rawLat = $request->post('latitude');
            $rawLng = $request->post('longitude');
            $latitude = ($rawLat !== null && $rawLat !== '') ? (float)$rawLat : null;
            $longitude = ($rawLng !== null && $rawLng !== '') ? (float)$rawLng : null;

            if (empty($latitude) || empty($longitude)) {
                $geo = FormatHelper::lookupCoordinates($location . ' ' . $province);
                $latitude = $geo['latitude'];
                $longitude = $geo['longitude'];
            }

            $tourData = [
                'category_id' => (int)$request->post('category_id', 0),
                'title' => (string)$request->post('title', ''),
                'slug' => (string)$request->post('slug', ''),
                'description' => (string)$request->post('description', ''),
                'price_mode' => (string)$request->post('price_mode', 'fixed_choice'),
                'base_price' => (float)$request->post('base_price', 0),
                'min_price' => (float)$request->post('min_price', 0),
                'max_price' => (float)$request->post('max_price', 0),
                'location' => $location,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'duration_days' => max(1, (int)$request->post('duration_days', 1)),
                'banner_image' => $bannerUrl,
            ];

            // Accommodation data
            $hasHomestay = $request->post('has_homestay') === '1';
            $accommodationData = [
                'has_homestay' => $hasHomestay,
                'homestay_name' => $hasHomestay ? (string)$request->post('homestay_name', '') : '',
                'address' => (string)$request->post('homestay_address', $tourData['location']),
                'room_type' => (string)$request->post('room_type', 'Phòng Tiêu Chuẩn'),
                'nights' => max(1, (int)$request->post('nights', 1)),
                'notes' => (string)$request->post('homestay_notes', ''),
            ];

            // Stops data with direct file upload & fallback URL support
            $stops = [];
            $stopImages = (array)$request->post('stop_image_urls', []);
            $stopCaptions = (array)$request->post('stop_captions', []);
            $stopStories = (array)$request->post('stop_stories', []);
            $stopVideos = (array)$request->post('stop_videos', []);
            $uploadedStopFiles = $_FILES['stop_files'] ?? null;

            $maxStops = max(
                count($stopImages),
                count($stopCaptions),
                (isset($uploadedStopFiles['name']) && is_array($uploadedStopFiles['name'])) ? count($uploadedStopFiles['name']) : 0
            );

            for ($i = 0; $i < $maxStops; $i++) {
                $caption = trim((string)($stopCaptions[$i] ?? ''));
                $story = trim((string)($stopStories[$i] ?? ''));
                $video = !empty($stopVideos[$i]) ? trim((string)$stopVideos[$i]) : null;
                $imageUrl = trim((string)($stopImages[$i] ?? ''));

                // Handle direct file upload if present
                if (isset($uploadedStopFiles['name'][$i]) && !empty($uploadedStopFiles['name'][$i]) && ($uploadedStopFiles['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name' => $uploadedStopFiles['name'][$i],
                        'type' => $uploadedStopFiles['type'][$i] ?? 'image/jpeg',
                        'tmp_name' => $uploadedStopFiles['tmp_name'][$i],
                        'error' => $uploadedStopFiles['error'][$i],
                        'size' => $uploadedStopFiles['size'][$i],
                    ];
                    $imageUrl = $this->tourService->handleStopImageUpload($singleFile);
                }

                if (!empty($imageUrl)) {
                    $stops[] = [
                        'image_url' => $imageUrl,
                        'caption' => $caption !== '' ? $caption : ('Điểm dừng ' . (count($stops) + 1)),
                        'story_text' => $story,
                        'video_url' => $video,
                        'sort_order' => count($stops) + 1,
                    ];
                }
            }

            $updated = $this->tourService->updateTour($hostId, $tourId, $tourData, $accommodationData, $stops);

            $this->flash('success', 'Cập nhật gói trải nghiệm "' . htmlspecialchars($updated['title']) . '" thành công!');
            $this->redirect('/host/services');
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
            $this->redirect('/host/services/' . $tourId . '/edit');
        }
    }

    /**
     * List all booking requests for the host's tours with status filtering.
     */
    public function bookings(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $hostId = (int)$user['id'];
        $status = $request->query('status');
        $status = !empty($status) && in_array($status, ['pending', 'awaiting_payment', 'confirmed', 'completed', 'cancelled', 'rejected', 'expired'], true) ? (string)$status : null;

        $serviceId = $request->query('service_id');
        $serviceId = !empty($serviceId) ? (int)$serviceId : null;

        $search = $request->query('q');
        $search = !empty($search) ? trim((string)$search) : null;

        $bookings = $this->bookingModel->getHostBookingsFiltered($hostId, $status, $serviceId, $search);
        $stats = $this->bookingModel->getHostBookingStats($hostId);
        $services = $this->tourService->getHostTours($hostId);

        $this->view('host/bookings/index', [
            'title' => 'Quản Lý Đơn Đặt Tour Bản Địa — OnlineTraveller Host',
            'user' => $user,
            'bookings' => $bookings,
            'stats' => $stats,
            'services' => $services,
            'filters' => [
                'status' => $status,
                'service_id' => $serviceId,
                'search' => $search,
            ],
        ]);
    }

    /**
     * View detailed booking information for Host.
     */
    public function showBooking(Request $request, string $id): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $hostId = (int)$user['id'];
        $bookingId = (int)$id;

        try {
            $booking = $this->bookingService->getBooking($bookingId, $hostId, 'host');

            $this->view('host/bookings/show', [
                'title' => "Chi Tiết Đơn Đặt #{$booking['booking_code']} — OnlineTraveller Host",
                'user' => $user,
                'booking' => $booking,
            ]);
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
            $this->redirect('/host/bookings');
        }
    }

    /**
     * Host approves booking request and initiates 15-minute seat reservation.
     */
    public function approveBooking(Request $request, string $id): void
    {
        $user = $this->user();
        if (!$user) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Vui lòng đăng nhập.'], 401);
                return;
            }
            $this->redirect('/login');
            return;
        }

        $hostId = (int)$user['id'];
        $bookingId = (int)$id;

        try {
            $approved = $this->bookingService->approveBooking($hostId, $bookingId);

            $message = 'Đã phê duyệt yêu cầu đặt tour thành công!';
            if (($approved['status'] ?? '') === 'awaiting_payment') {
                $message .= ' Đã giữ chỗ 15 phút để du khách tiến hành thanh toán MoMo Sandbox.';
            } else {
                $message .= ' Chuyến đi đã được xác nhận chính thức.';
            }

            if ($request->isAjax()) {
                $this->response->json([
                    'success' => true,
                    'message' => $message,
                    'booking' => $approved,
                ]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect('/host/bookings');
            return;
        } catch (Exception $e) {
            $errorMsg = match($e->getMessage()) {
                'NOT_ENOUGH_SEATS' => 'Không đủ số chỗ khả dụng trên lịch trình khởi hành này để duyệt thêm.',
                'BOOKING_NOT_PENDING' => 'Đơn đặt chỗ không còn ở trạng thái chờ duyệt.',
                'FORBIDDEN_NOT_HOST' => 'Bạn không có quyền quản lý đơn đặt này.',
                'BOOKING_NOT_FOUND' => 'Không tìm thấy đơn đặt chỗ yêu cầu.',
                default => 'Không thể phê duyệt đơn: ' . $e->getMessage(),
            };

            if ($request->isAjax()) {
                $this->response->json(['error' => $errorMsg], 400);
                return;
            }

            $this->flash('danger', $errorMsg);
            $this->redirect('/host/bookings');
            return;
        }
    }

    /**
     * Host rejects a pending booking request with explanation.
     */
    public function rejectBooking(Request $request, string $id): void
    {
        $user = $this->user();
        if (!$user) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Vui lòng đăng nhập.'], 401);
                return;
            }
            $this->redirect('/login');
            return;
        }

        $hostId = (int)$user['id'];
        $bookingId = (int)$id;
        $reason = trim((string)$request->post('reason', ''));

        try {
            $rejected = $this->bookingService->rejectBooking($hostId, $bookingId, $reason);

            $message = 'Đã từ chối đơn đặt tour thành công.';

            if ($request->isAjax()) {
                $this->response->json([
                    'success' => true,
                    'message' => $message,
                    'booking' => $rejected,
                ]);
                return;
            }

            $this->flash('warning', $message);
            $this->redirect('/host/bookings');
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Không thể từ chối đơn: ' . $e->getMessage()], 400);
                return;
            }

            $this->flash('danger', 'Không thể từ chối đơn: ' . $e->getMessage());
            $this->redirect('/host/bookings');
            return;
        }
    }
}

if (!class_exists('HostController', false)) {
    class_alias(HostController::class, 'HostController');
}
