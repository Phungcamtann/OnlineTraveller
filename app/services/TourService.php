<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Service;
use App\Models\Accommodation;
use App\Models\Category;
use RuntimeException;

/**
 * Tour & Experience Service: handles creation, updates, pricing mode enforcement,
 * accommodation setup (Homestay 1-1), 3D stops attachments, and image uploads.
 */
class TourService
{
    private Service $serviceModel;
    private Accommodation $accommodationModel;
    private Category $categoryModel;

    public function __construct(
        ?Service $serviceModel = null,
        ?Accommodation $accommodationModel = null,
        ?Category $categoryModel = null
    ) {
        $this->serviceModel = $serviceModel ?? new Service();
        $this->accommodationModel = $accommodationModel ?? new Accommodation();
        $this->categoryModel = $categoryModel ?? new Category();
    }

    /**
     * Host creates a new local tour package with pricing mode, optional homestay & 3D stops.
     *
     * @param int $hostId ID of the local host
     * @param array $data Main tour details
     * @param array|null $accommodationData Optional homestay lodging details
     * @param array $stops Optional 3D stations / panorama stops
     * @return array Created tour record
     * @throws RuntimeException If validation fails
     */
    public function createTour(
        int $hostId,
        array $data,
        ?array $accommodationData = null,
        array $stops = []
    ): array {
        $validated = $this->validateTourData($data);

        // Generate unique URL slug
        $suggestedSlug = !empty($data['slug']) ? (string)$data['slug'] : $validated['title'];
        $slug = $this->serviceModel->generateUniqueSlug($suggestedSlug);

        // Call Stored Procedure sp_services_create
        $created = $this->serviceModel->create(
            $hostId,
            $validated['category_id'],
            $validated['title'],
            $slug,
            $validated['description'],
            $validated['price_mode'],
            $validated['base_price'],
            $validated['min_price'],
            $validated['max_price'],
            $validated['location'],
            $validated['latitude'],
            $validated['longitude'],
            $validated['duration_days'],
            $validated['banner_image']
        );

        if (empty($created['id'])) {
            throw new RuntimeException('Không thể khởi tạo gói trải nghiệm. Vui lòng thử lại.');
        }

        $serviceId = (int)$created['id'];

        // Save Homestay accommodation if provided
        if (!empty($accommodationData['homestay_name'])) {
            $this->accommodationModel->save(
                $hostId,
                $serviceId,
                trim((string)$accommodationData['homestay_name']),
                trim((string)($accommodationData['address'] ?? $validated['location'])),
                trim((string)($accommodationData['room_type'] ?? 'Phòng Tiêu Chuẩn')),
                max(1, (int)($accommodationData['nights'] ?? 1)),
                !empty($accommodationData['notes']) ? trim((string)$accommodationData['notes']) : null
            );
        }

        // Attach 3D tour stops if provided
        if (!empty($stops) && is_array($stops)) {
            foreach ($stops as $index => $stop) {
                if (!empty($stop['image_url'])) {
                    $this->serviceModel->addStop(
                        $serviceId,
                        (string)$stop['image_url'],
                        (string)($stop['caption'] ?? ''),
                        !empty($stop['story_text']) ? (string)$stop['story_text'] : null,
                        !empty($stop['video_url']) ? (string)$stop['video_url'] : null,
                        (int)($stop['sort_order'] ?? ($index + 1))
                    );
                }
            }
        }

        return $this->getTourDetailForHost($hostId, $serviceId);
    }

    /**
     * Host updates an existing tour package.
     *
     * @throws RuntimeException If unauthorized or validation fails
     */
    public function updateTour(
        int $hostId,
        int $tourId,
        array $data,
        ?array $accommodationData = null,
        ?array $stops = null
    ): array {
        $existing = $this->serviceModel->getById($tourId);
        if (!$existing) {
            throw new RuntimeException('Gói trải nghiệm không tồn tại trên hệ thống.');
        }

        if ((int)$existing['host_id'] !== $hostId) {
            throw new RuntimeException('Bạn không có quyền chỉnh sửa gói trải nghiệm này.');
        }

        $validated = $this->validateTourData($data);

        // Keep or regenerate slug if title changed
        $slug = $existing['slug'];
        if (!empty($data['slug']) && $data['slug'] !== $existing['slug']) {
            $slug = $this->serviceModel->generateUniqueSlug((string)$data['slug'], $tourId);
        } elseif ($validated['title'] !== $existing['title']) {
            $slug = $this->serviceModel->generateUniqueSlug($validated['title'], $tourId);
        }

        // Retain existing banner if new banner is not uploaded
        $bannerImage = $validated['banner_image'] ?: $existing['banner_image'];

        // Call Stored Procedure sp_services_update
        $this->serviceModel->update(
            $hostId,
            $tourId,
            $validated['category_id'],
            $validated['title'],
            $slug,
            $validated['description'],
            $validated['price_mode'],
            $validated['base_price'],
            $validated['min_price'],
            $validated['max_price'],
            $validated['location'],
            $validated['latitude'],
            $validated['longitude'],
            $validated['duration_days'],
            $bannerImage
        );

        // Handle Homestay accommodation
        if (!empty($accommodationData['homestay_name'])) {
            $this->accommodationModel->save(
                $hostId,
                $tourId,
                trim((string)$accommodationData['homestay_name']),
                trim((string)($accommodationData['address'] ?? $validated['location'])),
                trim((string)($accommodationData['room_type'] ?? 'Phòng Tiêu Chuẩn')),
                max(1, (int)($accommodationData['nights'] ?? 1)),
                !empty($accommodationData['notes']) ? trim((string)$accommodationData['notes']) : null
            );
        } elseif (isset($accommodationData['has_homestay']) && !$accommodationData['has_homestay']) {
            $this->accommodationModel->delete($tourId);
        }

        // Handle 3D stops if explicitly provided
        if (is_array($stops)) {
            $this->serviceModel->clearStops($tourId);
            foreach ($stops as $index => $stop) {
                if (!empty($stop['image_url'])) {
                    $this->serviceModel->addStop(
                        $tourId,
                        (string)$stop['image_url'],
                        (string)($stop['caption'] ?? ''),
                        !empty($stop['story_text']) ? (string)$stop['story_text'] : null,
                        !empty($stop['video_url']) ? (string)$stop['video_url'] : null,
                        (int)($stop['sort_order'] ?? ($index + 1))
                    );
                }
            }
        }

        return $this->getTourDetailForHost($hostId, $tourId);
    }

    /**
     * Get full details of a tour belonging to host (including homestay and 3D stops).
     */
    public function getTourDetailForHost(int $hostId, int $tourId): array
    {
        $tour = $this->serviceModel->getById($tourId);
        if (!$tour) {
            throw new RuntimeException('Không tìm thấy gói trải nghiệm yêu cầu.');
        }

        if ((int)$tour['host_id'] !== $hostId) {
            throw new RuntimeException('Bạn không được cấp quyền xem gói trải nghiệm này.');
        }

        $tour['stops'] = $this->serviceModel->getStops($tourId);
        $tour['accommodation'] = $this->accommodationModel->getByServiceId($tourId);

        return $tour;
    }

    /**
     * Get all tours owned by a specific host.
     */
    public function getHostTours(int $hostId, ?string $status = null): array
    {
        return $this->serviceModel->getByHost($hostId, $status);
    }

    /**
     * Admin review / moderate tour status (sp_services_moderate).
     */
    public function moderateTour(int $adminId, int $tourId, string $status, ?string $rejectionReason = null): array
    {
        if (!in_array($status, ['approved', 'rejected', 'pending', 'draft', 'archived'], true)) {
            throw new RuntimeException('Trạng thái kiểm duyệt không hợp lệ.');
        }

        return $this->serviceModel->moderate($adminId, $tourId, $status, $rejectionReason);
    }

    /**
     * Handle banner image file upload with security and mime validation.
     *
     * @param array $file $_FILES item
     * @return string Relative URL path to saved image
     * @throws RuntimeException If file upload is invalid
     */
    public function handleBannerUpload(array $file): string
    {
        return $this->processImageUpload($file, 'tours');
    }

    /**
     * Handle 3D tour stop image file upload.
     */
    public function handleStopImageUpload(array $file): string
    {
        return $this->processImageUpload($file, 'tours/stops');
    }

    /**
     * Common image upload processor.
     */
    private function processImageUpload(array $file, string $subfolder): string
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Dữ liệu tệp tải lên không hợp lệ.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Lỗi tải tệp lên máy chủ: Mã lỗi ' . $file['error']);
        }

        // Max 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Dung lượng ảnh vượt quá giới hạn cho phép (tối đa 5MB).');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!array_key_exists($mimeType, $allowedMimes)) {
            throw new RuntimeException('Định dạng ảnh không được hỗ trợ. Vui lòng chọn ảnh JPG, PNG hoặc WEBP.');
        }

        $ext = $allowedMimes[$mimeType];
        $filename = 'img_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;

        $projectRoot = dirname(__DIR__, 2);
        $targetDir = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subfolder);

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new RuntimeException('Không thể khởi tạo thư mục lưu trữ ảnh.');
            }
        }

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Lưu tệp ảnh lên máy chủ thất bại.');
        }

        return '/assets/images/' . $subfolder . '/' . $filename;
    }

    /**
     * Validate and normalize raw tour data fields.
     */
    private function validateTourData(array $data): array
    {
        $title = trim((string)($data['title'] ?? ''));
        if (mb_strlen($title, 'UTF-8') < 5 || mb_strlen($title, 'UTF-8') > 255) {
            throw new RuntimeException('Tiêu đề gói trải nghiệm phải từ 5 đến 255 ký tự.');
        }

        $categoryId = (int)($data['category_id'] ?? 0);
        if ($categoryId <= 0) {
            throw new RuntimeException('Vui lòng chọn danh mục phù hợp cho tour.');
        }

        $location = trim((string)($data['location'] ?? ''));
        if (mb_strlen($location, 'UTF-8') < 3) {
            throw new RuntimeException('Địa điểm tổ chức không được để trống.');
        }

        $priceMode = (string)($data['price_mode'] ?? 'fixed_choice');
        $validModes = ['free', 'host_range', 'fixed_choice', 'prepaid_full'];
        if (!in_array($priceMode, $validModes, true)) {
            throw new RuntimeException('Chế độ định giá không hợp lệ.');
        }

        $basePrice = 0.0;
        $minPrice = 0.0;
        $maxPrice = 0.0;

        switch ($priceMode) {
            case 'free':
                $basePrice = 0.0;
                $minPrice = 0.0;
                $maxPrice = 0.0;
                break;

            case 'fixed_choice':
            case 'prepaid_full':
                $basePrice = (float)($data['base_price'] ?? 0);
                if ($basePrice <= 0) {
                    throw new RuntimeException('Vui lòng nhập giá trải nghiệm hợp lệ (> 0 VNĐ).');
                }
                $minPrice = 0.0;
                $maxPrice = 0.0;
                break;

            case 'host_range':
                $minPrice = (float)($data['min_price'] ?? 0);
                $maxPrice = (float)($data['max_price'] ?? 0);
                if ($minPrice <= 0 || $maxPrice <= $minPrice) {
                    throw new RuntimeException('Khoảng giá thỏa thuận không hợp lệ: Giá tối đa phải lớn hơn giá tối thiểu.');
                }
                $basePrice = $minPrice;
                break;
        }

        $durationDays = max(1, (int)($data['duration_days'] ?? 1));

        $latitude = null;
        if (isset($data['latitude']) && $data['latitude'] !== '') {
            $lat = (float)$data['latitude'];
            if ($lat >= -90.0 && $lat <= 90.0) {
                $latitude = $lat;
            }
        }

        $longitude = null;
        if (isset($data['longitude']) && $data['longitude'] !== '') {
            $lng = (float)$data['longitude'];
            if ($lng >= -180.0 && $lng <= 180.0) {
                $longitude = $lng;
            }
        }

        return [
            'category_id' => $categoryId,
            'title' => $title,
            'description' => trim((string)($data['description'] ?? '')),
            'price_mode' => $priceMode,
            'base_price' => $basePrice,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'location' => $location,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'duration_days' => $durationDays,
            'banner_image' => !empty($data['banner_image']) ? trim((string)$data['banner_image']) : null,
        ];
    }
}

if (!class_exists('TourService', false)) {
    class_alias(TourService::class, 'TourService');
}
