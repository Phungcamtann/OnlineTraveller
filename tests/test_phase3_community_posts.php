<?php

declare(strict_types=1);

/**
 * ============================================================================
 * KIỂM THỬ GIAI ĐOẠN 3: BẢN TIN CỘNG ĐỒNG 3 VAI TRÒ & TIN TỨC RSS VNEXPRESS
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/models/Post.php';
require_once __DIR__ . '/../app/integrations/RssClient.php';
require_once __DIR__ . '/../app/controllers/PostController.php';

use App\Helpers\SecurityHelper;
use App\Models\Post;
use App\Integrations\RssClient;
use App\Controllers\PostController;
use Core\Request;
use Core\Response;

Response::$preventExit = true;

$totalTests = 0;
$passedTests = 0;

function assertTest(string $description, bool $condition): void {
    global $totalTests, $passedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description}\n";
    }
}

echo "\n" . str_repeat('=', 84) . "\n";
echo "   CHẠY KIỂM THỬ GIAI ĐOẠN 3: BẢN TIN CỘNG ĐỒNG 3 VAI TRÒ & RSS VNEXPRESS   \n";
echo str_repeat('=', 84) . "\n\n";

$db = Core\Database::getConnection();
$postModel = new Post();
$createdPostIds = [];
$mockFilePaths = [];

// Get test users for the 3 roles
$tourist = $db->query("SELECT id, name, role FROM tbl_users WHERE role = 'tourist' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$touristId = (int)$tourist['id'];

$host = $db->query("SELECT id, name, role FROM tbl_users WHERE role = 'host' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$hostId = (int)$host['id'];

$admin = $db->query("SELECT id, name, role FROM tbl_users WHERE role = 'admin' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$adminId = (int)$admin['id'];

// ==============================================================================
// NHÓM 1: ĐĂNG BÀI BẢN TIN CHO CẢ 3 VAI TRÒ (pct_sp_community_posts_create)
// ==============================================================================
echo "--- NHÓM 1: ĐĂNG BÀI BẢN TIN CHO CẢ 3 VAI TRÒ (Tourist, Host, Admin) ---\n";

// 1.1 Du khách (Tourist) đăng bài chia sẻ
$touristPost = $postModel->createCommunityPost(
    $touristId,
    'Kinh nghiệm du lịch Sa Pa 3 ngày 2 đêm tự túc ' . time(),
    null,
    'Chuyến đi Sa Pa vừa rồi của mình thực sự là một kỷ niệm tuyệt vời không thể nào quên được...',
    '/assets/images/posts/sample_sapa.jpg'
);
$createdPostIds[] = (int)$touristPost['id'];

assertTest("Du khách (Tourist) đăng bài chia sẻ cộng đồng thành công", !empty($touristPost['id']));
assertTest("Bài viết của du khách có trạng thái xuất bản ngay (is_published = 1)", (int)$touristPost['is_published'] === 1);
assertTest("Hệ thống tự động sinh slug tiếng Việt chuẩn từ tiêu đề", !empty($touristPost['slug']));
assertTest("Bài viết lưu đúng author_id của du khách", (int)$touristPost['author_id'] === $touristId);

// 1.2 Chủ nhà (Host) đăng bài chia sẻ văn hóa bản địa
$hostPost = $postModel->createCommunityPost(
    $hostId,
    'Mùa hái chè cổ thụ Suối Giàng cùng người H Mông ' . time(),
    null,
    'Là người con sinh ra và lớn lên ở vùng chè cổ thụ Suối Giàng, mình muốn chia sẻ nét đẹp văn hóa...',
    '/assets/images/posts/sample_tea.jpg'
);
$createdPostIds[] = (int)$hostPost['id'];

assertTest("Chủ nhà bản địa (Host) đăng bài chia sẻ kinh nghiệm thành công", !empty($hostPost['id']));
assertTest("Bài viết của Host lưu đúng author_id của Host", (int)$hostPost['author_id'] === $hostId);

// 1.3 Ban quản trị (Admin) đăng bài qua phương thức cộng đồng
$adminPost = $postModel->createCommunityPost(
    $adminId,
    'Cảnh báo thời tiết mưa bão an toàn khi đi trekking ' . time(),
    null,
    'Ban Quản Trị xin gửi tới quý du khách và các Host địa phương những lưu ý an toàn quan trọng...',
    null
);
$createdPostIds[] = (int)$adminPost['id'];

assertTest("Ban Quản Trị (Admin) đăng bài thông báo cộng đồng thành công", !empty($adminPost['id']));

// 1.4 Kiểm tra hiển thị kèm vai trò tác giả (author_role)
$fetchedPost = $postModel->getById((int)$hostPost['id']);
assertTest("Truy vấn bài viết trả về đầy đủ author_name và author_role ('host')", 
    isset($fetchedPost['author_role']) && $fetchedPost['author_role'] === 'host');

// ==============================================================================
// NHÓM 2: UPLOAD TỆP ẢNH ĐẠI DIỆN BÀI VIẾT (Post::handleThumbnailUpload)
// ==============================================================================
echo "\n--- NHÓM 2: UPLOAD ẢNH ĐẠI DIỆN BÀI VIẾT (handleThumbnailUpload) ---\n";

$tempImgPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_post_thumb_' . uniqid() . '.jpg';
$minimalJpg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
file_put_contents($tempImgPath, $minimalJpg);
$mockFilePaths[] = $tempImgPath;

$mockUpload = [
    'name' => 'post_banner.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $tempImgPath,
    'error' => UPLOAD_ERR_OK,
    'size' => strlen($minimalJpg),
];

$savedThumbUrl = $postModel->handleThumbnailUpload($mockUpload);
assertTest("Upload ảnh đại diện bài viết trả về URL hợp lệ trong /assets/images/posts/", 
    str_starts_with($savedThumbUrl, '/assets/images/posts/'));

$savedLocalPath = dirname(__DIR__) . '/public' . $savedThumbUrl;
assertTest("Tệp ảnh bài viết được ghi thành công trên đĩa cứng", file_exists($savedLocalPath));
if (file_exists($savedLocalPath)) {
    $mockFilePaths[] = $savedLocalPath;
}

// ==============================================================================
// NHÓM 3: GIAO DIỆN TẠO BÀI VIẾT MỚI (app/views/posts/create.php)
// ==============================================================================
echo "\n--- NHÓM 3: GIAO DIỆN TẠO BÀI VIẾT (posts/create.php) ---\n";

$user = $tourist;
ob_start();
include __DIR__ . '/../app/views/posts/create.php';
$createHtml = ob_get_clean();

assertTest("Giao diện có tiêu đề gợi mở 'Kể Lại Chuyến Đi Của Bạn'", 
    str_contains($createHtml, 'Kể Lại Chuyến Đi Của Bạn'));
assertTest("Giao diện có ô tải file ảnh đại diện từ máy tính (name='thumbnail_file')", 
    str_contains($createHtml, 'name="thumbnail_file"'));
assertTest("Giao diện có ô nhập URL ảnh dự phòng (name='thumbnail')", 
    str_contains($createHtml, 'name="thumbnail"'));
assertTest("Giao diện có khung xem trước ảnh (id='postThumbPreviewImg')", 
    str_contains($createHtml, 'id="postThumbPreviewImg"'));
assertTest("Giao diện có ô nhập nội dung câu chuyện (name='content')", 
    str_contains($createHtml, 'name="content"'));
assertTest("Đã loại bỏ các ghi chú kỹ thuật về Stored Procedure hoặc cơ sở dữ liệu", 
    !str_contains($createHtml, 'pct_sp_') && !str_contains($createHtml, 'CSDL'));

// ==============================================================================
// NHÓM 4: GIAO DIỆN BẢN TIN KẾT HỢP RSS VNEXPRESS (posts/index.php)
// ==============================================================================
echo "\n--- NHÓM 4: GIAO DIỆN BẢN TIN & TÍCH HỢP RSS VNEXPRESS (posts/index.php) ---\n";

$postCtrl = new PostController(new Request());

// Test index with mock request
$_SERVER['REQUEST_METHOD'] = 'GET';
unset($_SERVER['HTTP_X_REQUESTED_WITH']);
$_GET = ['tab' => 'all'];

ob_start();
$postCtrl->index(new Request());
$indexHtml = ob_get_clean();

assertTest("Trang Bản tin có nút nổi bật 'Chia Sẻ Câu Chuyện / Viết Bài Mới'", 
    str_contains($indexHtml, 'Chia Sẻ Câu Chuyện / Viết Bài Mới'));
assertTest("Trang Bản tin có nút chuyển Tab 'Tất Cả Dòng Tin'", 
    str_contains($indexHtml, 'Tất Cả Dòng Tin'));
assertTest("Trang Bản tin có Tab 'Bài Viết Cộng Đồng'", 
    str_contains($indexHtml, 'Bài Viết Cộng Đồng'));
assertTest("Trang Bản tin có Tab 'Tin Tức VnExpress Du Lịch'", 
    str_contains($indexHtml, 'Tin Tức VnExpress Du Lịch'));
assertTest("Giao diện có huy hiệu nhận diện nguồn VnExpress", 
    str_contains($indexHtml, 'VnExpress'));
assertTest("Giao diện có huy hiệu vai trò tác giả cộng đồng ('Du Khách' hoặc 'Host Bản Địa')", 
    str_contains($indexHtml, 'Du Khách') || str_contains($indexHtml, 'Host Bản Địa'));

// ==============================================================================
// NHÓM 5: CONTROLLER STORE ACTION QUA FORM (POST /posts)
// ==============================================================================
echo "\n--- NHÓM 5: PHÂN QUYỀN VÀ SUBMIT QUA CONTROLLER (PostController::store) ---\n";

// 5.1 Khách vãng lai chưa đăng nhập bị chặn
$_SESSION = [];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST = [
    'title' => 'Bài viết của khách vãng lai',
    'content' => 'Nội dung chia sẻ khi chưa đăng nhập hệ thống...',
];

ob_start();
$postCtrl->store(new Request());
$guestResJson = ob_get_clean();
$guestRes = json_decode($guestResJson, true);

assertTest("Khách vãng lai chưa đăng nhập bị chặn (HTTP 401 / Vui lòng đăng nhập)", 
    isset($guestRes['error']) && str_contains($guestRes['error'], 'Vui lòng đăng nhập'));

// 5.2 Tourist đăng nhập submit thành công qua POST /posts
$_SESSION['user'] = $tourist;
$_SESSION['user_id'] = $touristId;
$_SESSION['role'] = 'tourist';

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST = [
    '_csrf_token' => SecurityHelper::csrfToken(),
    'title' => 'Review hành trình săn mây Cầu Đất đỉnh nóc ' . time(),
    'content' => 'Chuyến đi săn mây Cầu Đất cùng anh hướng dẫn viên bản địa siêu nhiệt tình và chuyên nghiệp...',
    'thumbnail' => $savedThumbUrl,
];

ob_start();
$postCtrl->store(new Request());
$touristResJson = ob_get_clean();
$touristRes = json_decode($touristResJson, true);

assertTest("Tourist đăng bài thành công qua PostController::store (AJAX trả về success)", 
    !empty($touristRes['success']) && isset($touristRes['post']['id']));

if (!empty($touristRes['post']['id'])) {
    $createdPostIds[] = (int)$touristRes['post']['id'];
}

// Clean up created records & mock files
if (!empty($createdPostIds)) {
    $idsStr = implode(',', $createdPostIds);
    $db->exec("DELETE FROM tbl_posts WHERE id IN ({$idsStr})");
}
foreach ($mockFilePaths as $filePath) {
    if (file_exists($filePath)) {
        @unlink($filePath);
    }
}
echo "  [INFO] Đã dọn dẹp sạch sẽ toàn bộ bản ghi và tệp kiểm thử Giai đoạn 3.\n";

// ==============================================================================
// TỔNG KẾT
// ==============================================================================
echo "\n" . str_repeat('=', 84) . "\n";
echo "KẾT QUẢ KIỂM THỬ GIAI ĐOẠN 3: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ GIAI ĐOẠN 3 ĐẠT CHUẨN 100%! <<<\n";
} else {
    echo ">>> CÓ BÀI KIỂM THỬ THẤT BẠI! <<<\n";
}
echo str_repeat('=', 84) . "\n\n";

exit($passedTests === $totalTests ? 0 : 1);
