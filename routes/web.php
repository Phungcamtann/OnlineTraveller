<?php

declare(strict_types=1);

use Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ServiceController;
use App\Controllers\AuthController;
use App\Controllers\HostController;
use App\Controllers\ScheduleController;
use App\Controllers\BookingController;
use App\Controllers\AdminController;
use App\Controllers\CategoryController;
use App\Controllers\PaymentController;
use App\Controllers\ReceiptController;
use App\Controllers\RefundController;
use App\Controllers\DiscussionController;
use App\Controllers\ReviewController;
use App\Controllers\PostController;
use App\Middleware\AuthMiddleware;
use App\Middleware\HostMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\CsrfMiddleware;

return function (Router $router): void {
    // 1. PUBLIC STOREFRONT (TV4 & TV2)
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/services', [ServiceController::class, 'index']);
    $router->get('/services/{id}', [ServiceController::class, 'show']);
    $router->get('/services/{id}/virtual-tour', [ServiceController::class, 'virtualTour']);
    $router->get('/services/{id}/3d', [ServiceController::class, 'virtualTour']);

    // 2. AUTHENTICATION & PROFILE (TV1)
    $router->get('/login', [AuthController::class, 'loginForm']);
    $router->post('/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);
    $router->get('/register', [AuthController::class, 'registerForm']);
    $router->post('/register', [AuthController::class, 'register'], [CsrfMiddleware::class]);
    $router->get('/profile', [AuthController::class, 'profile'], [AuthMiddleware::class]);
    $router->post('/profile', [AuthController::class, 'updateProfile'], [AuthMiddleware::class, CsrfMiddleware::class]);
    $router->get('/logout', [AuthController::class, 'logout']);

    // 3. HOST DASHBOARD, TOURS & SCHEDULES (TV2)
    $router->get('/host', [HostController::class, 'dashboard'], [AuthMiddleware::class, HostMiddleware::class]);
    $router->get('/host/bookings', [HostController::class, 'bookings'], [AuthMiddleware::class, HostMiddleware::class]);
    $router->get('/host/bookings/{id}', [HostController::class, 'showBooking'], [AuthMiddleware::class, HostMiddleware::class]);
    $router->post('/host/bookings/{id}/approve', [HostController::class, 'approveBooking'], [AuthMiddleware::class, HostMiddleware::class, CsrfMiddleware::class]);
    $router->post('/host/bookings/{id}/reject', [HostController::class, 'rejectBooking'], [AuthMiddleware::class, HostMiddleware::class, CsrfMiddleware::class]);
    $router->get('/host/services', [HostController::class, 'services'], [AuthMiddleware::class, HostMiddleware::class]);
    $router->get('/host/services/create', [HostController::class, 'createService'], [AuthMiddleware::class, HostMiddleware::class]);
    $router->post('/host/services', [HostController::class, 'storeService'], [AuthMiddleware::class, HostMiddleware::class, CsrfMiddleware::class]);
    $router->get('/host/services/{id}/edit', [HostController::class, 'editService'], [AuthMiddleware::class, HostMiddleware::class]);
    $router->post('/host/services/{id}/edit', [HostController::class, 'updateService'], [AuthMiddleware::class, HostMiddleware::class, CsrfMiddleware::class]);
    $router->get('/host/schedules', [ScheduleController::class, 'index'], [AuthMiddleware::class, HostMiddleware::class]);
    $router->get('/host/schedules/create', [ScheduleController::class, 'create'], [AuthMiddleware::class, HostMiddleware::class]);
    $router->post('/host/schedules', [ScheduleController::class, 'store'], [AuthMiddleware::class, HostMiddleware::class, CsrfMiddleware::class]);
    $router->post('/host/schedules/{id}/cancel', [ScheduleController::class, 'cancel'], [AuthMiddleware::class, HostMiddleware::class, CsrfMiddleware::class]);

    // 4. ADMIN DASHBOARD & MODERATION (TV1 & TV4)
    $router->get('/admin', [AdminController::class, 'dashboard'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/dashboard', [AdminController::class, 'dashboard'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/services', [AdminController::class, 'services'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/services/{id}/moderate', [AdminController::class, 'moderateService'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
    $router->get('/admin/banner', [AdminController::class, 'banner'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/banner', [AdminController::class, 'updateBanner'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
    $router->get('/admin/users', [AdminController::class, 'users'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/users/{id}/status', [AdminController::class, 'setUserStatus'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
    $router->get('/admin/categories', [CategoryController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/categories/create', [CategoryController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/categories', [CategoryController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
    $router->get('/admin/categories/{id}/edit', [CategoryController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/categories/{id}/edit', [CategoryController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
    $router->post('/admin/categories/{id}/toggle', [CategoryController::class, 'toggle'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);

    // 5. TOURIST BOOKINGS & RESERVATION FLOW (TV3)
    $router->get('/bookings', [BookingController::class, 'myBookings'], [AuthMiddleware::class]);
    $router->get('/bookings/create', [BookingController::class, 'create'], [AuthMiddleware::class]);
    $router->post('/bookings', [BookingController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
    $router->get('/bookings/{id}', [BookingController::class, 'show'], [AuthMiddleware::class]);
    $router->post('/bookings/{id}/cancel', [BookingController::class, 'cancel'], [AuthMiddleware::class, CsrfMiddleware::class]);
    $router->get('/my-bookings', [BookingController::class, 'myBookings'], [AuthMiddleware::class]);

    // 6. MOMO SANDBOX CHECKOUT & SIMULATION (TV3)
    $router->get('/payments/checkout', [PaymentController::class, 'checkout'], [AuthMiddleware::class]);
    $router->post('/payments/checkout', [PaymentController::class, 'checkout'], [AuthMiddleware::class, CsrfMiddleware::class]);
    $router->get('/payments/sandbox-qr', [PaymentController::class, 'sandboxQr'], [AuthMiddleware::class]);

    // 7. ELECTRONIC RECEIPTS & CANCELLATION REFUNDS (TV3-06)
    $router->get('/receipts/{id}', [ReceiptController::class, 'show'], [AuthMiddleware::class]);
    $router->get('/bookings/{id}/receipt', [ReceiptController::class, 'showByBooking'], [AuthMiddleware::class]);
    $router->get('/refunds/{id}', [RefundController::class, 'show'], [AuthMiddleware::class]);
    $router->get('/admin/refunds', [AdminController::class, 'refunds'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/refunds/{id}/approve', [AdminController::class, 'approveRefund'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);

    // 8. COMMUNITY DISCUSSIONS & REPLIES (TV4-05)
    $router->get('/services/{id}/discussions', [DiscussionController::class, 'index']);
    $router->post('/discussions', [DiscussionController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);

    // 9. REVIEWS & POST-TRIP STAR RATINGS (TV4-06)
    $router->post('/reviews', [ReviewController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
    $router->get('/services/{id}/reviews', [ReviewController::class, 'index']);

    // 10. TRAVEL BLOG & COMMUNITY POSTS (TV4-06 & Phase 3)
    $router->get('/posts', [PostController::class, 'index']);
    $router->get('/posts/create', [PostController::class, 'create'], [AuthMiddleware::class]);
    $router->post('/posts', [PostController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);
    $router->get('/posts/{slug}', [PostController::class, 'show']);
    $router->get('/admin/posts', [PostController::class, 'adminIndex'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/posts/create', [PostController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/posts', [PostController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
    $router->get('/admin/posts/{id}/edit', [PostController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/posts/{id}/edit', [PostController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
    $router->post('/admin/posts/{id}/delete', [PostController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
};
