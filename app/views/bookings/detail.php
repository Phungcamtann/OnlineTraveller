<?php
/**
 * View: Chi Tiết Đơn Đặt Chỗ & Tóm Tắt Thanh Toán (TV3-04)
 * File: app/views/bookings/detail.php
 *
 * @var array $booking Dữ liệu đơn đặt tour kèm liên kết dịch vụ, lịch, host và thanh toán
 * @var array $user Thông tin người dùng đăng nhập
 */

$code = htmlspecialchars($booking['booking_code'], ENT_QUOTES, 'UTF-8');
$status = (string)$booking['status'];
$unitPrice = (int)$booking['unit_price'];
$count = (int)$booking['participants_count'];
$grossTotal = $unitPrice * $count;
$discount = (int)$booking['discount_amount'];
$totalPrice = (int)$booking['total_price'];
$deposit = (int)$booking['deposit_amount'];
$remaining = (int)$booking['remaining_amount'];
$plan = (string)$booking['payment_plan'];
?>

<style>
@media print {
    /* 1. Hide interactive / navigation elements */
    .top-floating-header,
    header,
    footer,
    nav[aria-label="breadcrumb"],
    .breadcrumb,
    .no-print,
    .btn,
    button,
    #cancelModal,
    .modal,
    .modal-backdrop,
    .alert-primary a.btn,
    .alert-warning a.btn {
        display: none !important;
    }

    /* 2. Page & Layout Reset for standard A4 */
    @page {
        size: A4 portrait;
        margin: 12mm 10mm 12mm 10mm;
    }
    html, body {
        background: #ffffff !important;
        color: #111827 !important;
        font-size: 11pt !important;
        line-height: 1.4 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .container {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* 3. Card & Box Styling for Print */
    .card {
        border: 1px solid #d1d5db !important;
        box-shadow: none !important;
        background: #ffffff !important;
        page-break-inside: avoid;
    }
    .shadow-sm, .shadow {
        box-shadow: none !important;
    }
    .border-0 {
        border: 1px solid #e5e7eb !important;
    }
    .bg-light {
        background-color: #f9fafb !important;
    }

    /* 4. Column display in Print (maintain side-by-side layout) */
    .row {
        display: flex !important;
        flex-wrap: nowrap !important;
    }
    .col-lg-8 {
        width: 64% !important;
        flex: 0 0 64% !important;
    }
    .col-lg-4 {
        width: 36% !important;
        flex: 0 0 36% !important;
    }

    /* 5. Print watermark & voucher header */
    .print-receipt-header {
        display: block !important;
        border-bottom: 2px solid #10b981 !important;
        padding-bottom: 12px !important;
        margin-bottom: 20px !important;
    }
}

@media screen {
    .print-receipt-header {
        display: none !important;
    }
}
</style>

<div class="container py-4">
    <!-- PRINT ONLY HEADER (PHIẾU ĐẶT CHỖ CHUẨN IN A4) -->
    <div class="print-receipt-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <img src="/assets/images/logo.svg" alt="OnlineTraveller" width="44" height="44" class="rounded-circle">
                <div>
                    <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">ONLINETRAVELLER</h3>
                    <div class="small text-muted">Nền Tảng Du Lịch &amp; Trải Nghiệm Cùng Dân Bản Địa</div>
                    <div class="small text-muted">Hotline CSKH: 1900 6868 | Email: hotro@onlinetraveller.vn</div>
                </div>
            </div>
            <div class="text-end">
                <span class="badge bg-dark text-white px-3 py-1 fs-6 font-monospace mb-1 d-inline-block">
                    #<?= $code ?>
                </span>
                <div class="fw-bold text-success">PHIẾU XÁC NHẬN ĐẶT TOUR</div>
                <div class="small text-muted">Ngày in: <?= date('d/m/Y H:i') ?></div>
            </div>
        </div>
    </div>

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4 no-print">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted"><i class="bi bi-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/my-bookings" class="text-decoration-none text-muted">Chuyến đi của tôi</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Đơn #<?= $code ?></li>
        </ol>
    </nav>

    <!-- HEADER TITLE & BOOKING CODE -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 border-bottom pb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark text-white rounded-pill px-3 py-1 font-monospace">
                    #<?= $code ?>
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill no-print" onclick="navigator.clipboard.writeText('<?= $code ?>'); alert('Đã sao chép mã đơn hàng: <?= $code ?>');">
                    <i class="bi bi-copy"></i>
                </button>
            </div>
            <h3 class="fw-bold font-editorial text-dark mb-0">Chi Tiết Đơn Đặt Chỗ</h3>
            <small class="text-muted">
                Thời gian tạo: <?= date('H:i - d/m/Y', strtotime($booking['created_at'])) ?>
            </small>
        </div>

        <div class="d-flex align-items-center gap-2 no-print">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm" onclick="window.print();">
                <i class="bi bi-printer me-1"></i> In Đơn
            </button>
            <a href="/my-bookings" class="btn btn-outline-dark rounded-pill px-3 py-2 btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Danh Sách Chuyến Đi
            </a>
        </div>
    </div>

    <!-- STATUS BANNER & ALERT -->
    <?php if ($status === 'pending'): ?>
        <div class="alert alert-warning border-0 rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex align-items-start gap-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-circle flex-shrink-0" style="width: 46px; height: 46px;">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                            ĐANG CHỜ HOST DUYỆT GIỮ CHỖ
                        </span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Yêu cầu của bạn đang được chuyển tới Dân bản địa!</h6>
                    <p class="small text-muted mb-0">
                        Host <strong><?= htmlspecialchars($booking['host_name'], ENT_QUOTES, 'UTF-8') ?></strong> sẽ kiểm tra lịch và duyệt giữ chỗ trong ít phút. Sau khi được duyệt, hệ thống sẽ mở cổng thanh toán cọc trong vòng 15 phút.
                    </p>
                </div>
            </div>
        </div>
    <?php elseif ($status === 'awaiting_payment'): ?>
        <div class="alert alert-primary border-primary rounded-4 shadow-sm p-4 mb-4 bg-primary-subtle bg-opacity-25">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle flex-shrink-0" style="width: 46px; height: 46px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <span class="badge bg-primary px-3 py-1 rounded-pill fw-bold mb-1">
                            HOST ĐÃ DUYỆT — CHỜ THANH TOÁN TIỀN CỌC
                        </span>
                        <h6 class="fw-bold text-dark mb-1">Host đã giữ <?= $count ?> chỗ trống cho bạn!</h6>
                        <p class="small text-muted mb-0">
                            Vui lòng hoàn tất thanh toán tiền cọc <strong><?= FormatHelper::formatVnd($deposit) ?></strong> trước khi hết thời hạn giữ chỗ:
                            <strong class="text-danger" id="countdown-text">
                                <?= $booking['payment_expires_at'] ? date('H:i - d/m/Y', strtotime($booking['payment_expires_at'])) : '15 phút' ?>
                            </strong>
                        </p>
                    </div>
                </div>
                <div>
                    <a href="/payments/checkout?booking_id=<?= (int)$booking['id'] ?>" class="btn btn-success btn-lg rounded-pill px-4 shadow-sm fw-semibold no-print">
                        <i class="bi bi-wallet2 me-2"></i> Thanh Toán Cọc MoMo (<?= FormatHelper::formatVnd($deposit) ?>)
                    </a>
                </div>
            </div>
        </div>
    <?php elseif ($status === 'confirmed'): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle flex-shrink-0" style="width: 46px; height: 46px;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="badge bg-success px-3 py-1 rounded-pill fw-bold mb-1">
                            ĐÃ XÁC NHẬN ĐẶT CHỖ THÀNH CÔNG
                        </span>
                        <h6 class="fw-bold text-dark mb-1">Chuyến đi của bạn đã được xác nhận và đảm bảo!</h6>
                        <p class="small text-muted mb-0">
                            Bạn đã đặt cọc thành công qua MoMo Sandbox. 
                            <?php if (!empty($booking['receipt_code'])): ?>
                                Mã biên nhận điện tử: <strong class="font-monospace text-dark">#<?= htmlspecialchars($booking['receipt_code']) ?></strong>.
                            <?php endif; ?>
                            Vui lòng có mặt đúng giờ tại điểm hẹn đã thỏa thuận.
                        </p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="/bookings/<?= (int)$booking['id'] ?>/receipt" class="btn btn-outline-success rounded-pill px-3 py-2 btn-sm fw-semibold shadow-sm">
                        <i class="bi bi-receipt me-1"></i> Xem Biên Nhận Điện Tử
                    </a>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2 btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="bi bi-x-circle me-1"></i> Hủy Đặt Chỗ
                    </button>
                </div>
            </div>
        </div>
    <?php elseif ($status === 'cancelled'): ?>
        <div class="alert alert-secondary border-0 rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-secondary text-white rounded-circle flex-shrink-0" style="width: 46px; height: 46px;">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="badge bg-secondary px-3 py-1 rounded-pill fw-bold mb-1">
                            ĐƠN ĐẶT CHỖ ĐÃ BỊ HỦY
                        </span>
                        <h6 class="fw-bold text-dark mb-1">Yêu cầu giữ chỗ này đã bị hủy.</h6>
                        <?php if (!empty($booking['cancel_reason'])): ?>
                            <p class="small text-danger mb-1">
                                <strong>Lý do hủy:</strong> <?= htmlspecialchars($booking['cancel_reason'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($booking['refund_code'])): ?>
                            <p class="small text-muted mb-0">
                                <i class="bi bi-arrow-return-left me-1"></i> Hồ sơ hoàn tiền: <strong class="text-dark">#<?= htmlspecialchars($booking['refund_code']) ?></strong> 
                                — Số tiền: <strong class="text-success"><?= FormatHelper::formatVnd((float)$booking['refund_amount']) ?></strong> 
                                (Trạng thái: <span class="badge bg-info-subtle text-info border"><?= htmlspecialchars($booking['refund_status']) ?></span>)
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($booking['refund_id'])): ?>
                    <div>
                        <a href="/refunds/<?= (int)$booking['refund_id'] ?>" class="btn btn-outline-info rounded-pill px-3 py-2 btn-sm fw-semibold">
                            <i class="bi bi-arrow-return-left me-1"></i> Phiếu Theo Dõi Hoàn Tiền
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($status === 'rejected'): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex align-items-start gap-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle flex-shrink-0" style="width: 46px; height: 46px;">
                    <i class="bi bi-slash-circle-fill fs-4"></i>
                </div>
                <div>
                    <span class="badge bg-danger px-3 py-1 rounded-pill fw-bold mb-1">
                        HOST KHÔNG THỂ TIẾP NHẬN
                    </span>
                    <h6 class="fw-bold text-dark mb-1">Host rất tiếc không thể tiếp đón đợt này.</h6>
                    <?php if (!empty($booking['rejection_reason'])): ?>
                        <p class="small text-danger mb-0">
                            <strong>Lý do từ chối:</strong> <?= htmlspecialchars($booking['rejection_reason'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif ($status === 'completed'): ?>
        <div class="alert alert-info border-0 rounded-4 shadow-sm p-4 mb-4 bg-info-subtle bg-opacity-25">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-info text-white rounded-circle flex-shrink-0" style="width: 46px; height: 46px;">
                        <i class="bi bi-flag-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="badge bg-info text-dark px-3 py-1 rounded-pill fw-bold mb-1">
                            CHUYẾN ĐI ĐÃ HOÀN THÀNH
                        </span>
                        <h6 class="fw-bold text-dark mb-1">Cảm ơn bạn đã tham gia trải nghiệm bản địa!</h6>
                        <p class="small text-muted mb-0">
                            Chuyến đi đã kết thúc tốt đẹp. Đánh giá của bạn sẽ giúp cộng đồng có thêm góc nhìn chân thực và giúp Host hoàn thiện dịch vụ.
                        </p>
                    </div>
                </div>
                <div>
                    <a href="/bookings/<?= (int)$booking['id'] ?>/receipt" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm fw-semibold">
                        <i class="bi bi-receipt me-1"></i> Xem Biên Nhận
                    </a>
                </div>
            </div>
        </div>

        <!-- REVIEW SECTION FOR COMPLETED BOOKING -->
        <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                <h5 class="fw-bold font-editorial text-dark mb-0">
                    <i class="bi bi-star-fill text-warning me-2"></i>Đánh Giá Trải Nghiệm Sau Chuyến Đi
                </h5>
            </div>

            <?php if (!empty($booking['review_id'])): ?>
                <!-- ĐÃ ĐÁNH GIÁ -->
                <div class="p-3 bg-light rounded-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-1 text-warning fs-5">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi <?= $i <= (int)$booking['review_rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                            <?php endfor; ?>
                            <span class="badge bg-warning text-dark ms-2 font-monospace"><?= (int)$booking['review_rating'] ?> / 5 sao</span>
                        </div>
                        <small class="text-muted">
                            Đã đánh giá: <?= date('H:i - d/m/Y', strtotime($booking['review_created_at'])) ?>
                        </small>
                    </div>
                    <p class="text-dark mb-0 ps-1 fst-italic">
                        "<?= htmlspecialchars($booking['review_comment'], ENT_QUOTES, 'UTF-8') ?>"
                    </p>
                </div>
            <?php else: ?>
                <!-- CHƯA ĐÁNH GIÁ: FORM GỬI ĐÁNH GIÁ -->
                <form action="/reviews" method="POST" id="review-form">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                    <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Bạn chấm chuyến đi mấy sao?</label>
                        <div class="d-flex align-items-center gap-3">
                            <select name="rating" class="form-select rounded-pill px-3 fw-bold text-warning" style="max-width: 220px;" required>
                                <option value="5" selected>★★★★★ 5 sao (Tuyệt vời)</option>
                                <option value="4">★★★★☆ 4 sao (Rất tốt)</option>
                                <option value="3">★★★☆☆ 3 sao (Bình thường)</option>
                                <option value="2">★★☆☆☆ 2 sao (Chưa hài lòng)</option>
                                <option value="1">★☆☆☆☆ 1 sao (Thất vọng)</option>
                            </select>
                            <small class="text-muted">Chọn từ 1 đến 5 sao theo cảm nhận thực tế của bạn.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="review_comment" class="form-label small fw-semibold text-dark">Nhận xét chi tiết chuyến đi:</label>
                        <textarea class="form-control rounded-3" 
                                  id="review_comment" 
                                  name="comment" 
                                  rows="3" 
                                  placeholder="Chia sẻ về sự nhiệt tình của Host, cảnh đẹp, bữa ăn, trạm trải nghiệm..." 
                                  required></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold text-dark shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Gửi Đánh Giá Sao
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 2-COLUMN MAIN CONTENT -->
    <div class="row g-4">
        <!-- LEFT COLUMN: TOUR & SCHEDULE DETAILS -->
        <div class="col-lg-7">
            <!-- Tour Information Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <i class="bi bi-compass text-primary me-2"></i> Gói Trải Nghiệm Bản Địa
                </h6>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <?php if (!empty($booking['service_banner'])): ?>
                        <img src="<?= htmlspecialchars($booking['service_banner'], ENT_QUOTES, 'UTF-8') ?>" 
                             alt="<?= htmlspecialchars($booking['service_title'], ENT_QUOTES, 'UTF-8') ?>" 
                             class="rounded-3 object-fit-cover shadow-sm flex-shrink-0"
                             style="width: 100%; max-width: 160px; height: 110px;">
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold font-editorial text-dark mb-1">
                            <a href="/services/<?= (int)$booking['service_id'] ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($booking['service_title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h5>
                        <p class="small text-muted mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($booking['service_location'], ENT_QUOTES, 'UTF-8') ?> • 
                            <i class="bi bi-clock me-1"></i> <?= (int)$booking['duration_days'] ?> ngày
                        </p>
                        <span class="badge bg-light text-dark border">
                            Chế độ giá: <?= htmlspecialchars($booking['price_mode']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Schedule & Meeting Point Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <i class="bi bi-calendar-check text-primary me-2"></i> Lịch Trình Khởi Hành & Điểm Đón
                </h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block mb-1">THỜI GIAN KHỞI HÀNH</small>
                            <strong class="text-dark fs-6 d-block">
                                <i class="bi bi-clock me-1 text-primary"></i>
                                <?= date('H:i - d/m/Y', strtotime($booking['starts_at'])) ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block mb-1">THỜI GIAN KẾT THÚC</small>
                            <strong class="text-dark fs-6 d-block">
                                <i class="bi bi-clock-history me-1 text-secondary"></i>
                                <?= date('H:i - d/m/Y', strtotime($booking['ends_at'])) ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block mb-1">ĐIỂM TẬP TRUNG / HẸN ĐÓN</small>
                            <strong class="text-dark d-block">
                                <i class="bi bi-pin-map-fill me-1 text-danger"></i>
                                <?= htmlspecialchars($booking['meeting_point'], ENT_QUOTES, 'UTF-8') ?>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Host & Tourist Contact Card -->
            <div class="row g-4 mb-4">
                <!-- Host Contact -->
                <div class="col-sm-6">
                    <div class="card border-0 rounded-4 shadow-sm p-3 h-100 bg-white">
                        <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                            <i class="bi bi-person-badge text-success me-1"></i> Người Dân Bản Địa
                        </h6>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle fs-6" style="width: 38px; height: 38px;">
                                <i class="bi bi-person-fill text-warning"></i>
                            </div>
                            <div>
                                <strong class="text-dark d-block small"><?= htmlspecialchars($booking['host_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <small class="text-success"><i class="bi bi-patch-check-fill"></i> Đã xác thực</small>
                            </div>
                        </div>
                        <div class="small text-muted vstack gap-1">
                            <div><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($booking['host_phone'] ?? 'Chưa cập nhật') ?></div>
                            <div><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($booking['host_email']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Tourist Info -->
                <div class="col-sm-6">
                    <div class="card border-0 rounded-4 shadow-sm p-3 h-100 bg-white">
                        <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                            <i class="bi bi-person text-primary me-1"></i> Du Khách Đặt Chỗ
                        </h6>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle fs-6" style="width: 38px; height: 38px;">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div>
                                <strong class="text-dark d-block small"><?= htmlspecialchars($booking['tourist_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <small class="text-muted"><?= $count ?> khách tham gia</small>
                            </div>
                        </div>
                        <div class="small text-muted vstack gap-1">
                            <div><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($booking['tourist_phone'] ?? 'Chưa cập nhật') ?></div>
                            <div><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($booking['tourist_email']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: PRICE & PAYMENT BREAKDOWN -->
        <div class="col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">
                    BẢNG KÊ KHAI TÀI CHÍNH
                </span>
                <h5 class="fw-bold font-editorial text-dark mb-3">Chi Tiết Thanh Toán</h5>

                <div class="vstack gap-2 border-bottom pb-3 mb-3 small">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Đơn giá / người:</span>
                        <strong><?= FormatHelper::formatVnd($unitPrice) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Số lượng khách:</span>
                        <strong><?= $count ?> người</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tạm tính tiền tour:</span>
                        <strong><?= FormatHelper::formatVnd($grossTotal) ?></strong>
                    </div>
                    <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between text-success">
                            <span><i class="bi bi-tag-fill me-1"></i> Ưu đãi giảm giá:</span>
                            <strong>-<?= FormatHelper::formatVnd($discount) ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php
                    $vatData = FormatHelper::calculateVat($totalPrice, 0.08);
                    ?>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.8rem;">
                        <span>Tiền dịch vụ trước thuế (Net):</span>
                        <span><?= FormatHelper::formatVnd($vatData['net']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.8rem;">
                        <span>Thuế GTGT (VAT 8%):</span>
                        <span><?= FormatHelper::formatVnd($vatData['vat']) ?></span>
                    </div>
                </div>

                <!-- Total Summary -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <div>
                            <span class="fw-bold text-dark d-block">Tổng giá trị đơn:</span>
                            <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-check-circle me-1"></i>Đã bao gồm 8% thuế GTGT</small>
                        </div>
                        <span class="fs-4 fw-bold font-editorial text-dark">
                            <?= FormatHelper::formatVnd($totalPrice) ?>
                        </span>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-success">
                                <i class="bi bi-credit-card me-1"></i> Tiền cọc online:
                            </span>
                            <div class="text-end">
                                <span class="fs-5 fw-bold text-success d-block">
                                    <?= FormatHelper::formatVnd($deposit) ?>
                                </span>
                                <?php if ($status === 'confirmed'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0 px-2" style="font-size: 0.72rem;">Đã thanh toán MoMo</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill py-0 px-2" style="font-size: 0.72rem;">Chưa thanh toán</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center small text-muted border-top pt-2 mt-2">
                            <span>Còn lại gửi Host khi gặp:</span>
                            <strong class="text-dark">
                                <?= FormatHelper::formatVnd($remaining) ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Actions for Cancel or Back -->
                <div class="no-print">
                    <?php if (in_array($status, ['pending', 'awaiting_payment'], true)): ?>
                        <button type="button" 
                                class="btn btn-outline-danger w-100 py-2 rounded-pill fw-semibold btn-sm mb-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle me-1"></i> Hủy Đặt Chỗ Này
                        </button>
                    <?php endif; ?>

                    <a href="/services" class="btn btn-light w-100 py-2 rounded-pill text-muted small">
                        <i class="bi bi-search me-1"></i> Tìm Kiếm Gói Tour Khác
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CANCEL BOOKING MODAL -->
<?php if (in_array($status, ['pending', 'awaiting_payment', 'confirmed'], true)): ?>
<div class="modal fade no-print" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="/bookings/<?= (int)$booking['id'] ?>/cancel" method="POST">
                <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                <input type="hidden" name="csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title font-editorial fw-bold" id="cancelModalLabel">Xác Nhận Hủy Đặt Chỗ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <?php if ($status === 'confirmed'): ?>
                        <div class="alert alert-warning border-0 rounded-3 p-3 small mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            <strong>Chính sách hoàn tiền:</strong><br>
                            - Hủy <strong>trước 24 giờ</strong> so với giờ khởi hành (trước mốc T24): Hoàn toàn bộ số tiền đã thanh toán online qua MoMo.<br>
                            - Hủy <strong>sau mốc 24 giờ</strong> (từ T24 trở đi): Yêu cầu sẽ được chuyển đến Ban Quản Trị xem xét ngoại lệ.
                        </div>
                    <?php else: ?>
                        <p class="small text-muted mb-3">
                            Bạn có chắc chắn muốn hủy đơn đặt chỗ <strong>#<?= $code ?></strong>? Ghế trống đã giữ sẽ được tự động hoàn trả lại cho những du khách khác.
                        </p>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label small fw-semibold">Lý do hủy đơn:</label>
                        <textarea class="form-control rounded-3" 
                                  id="cancel_reason" 
                                  name="reason" 
                                  rows="3" 
                                  placeholder="VD: Thay đổi kế hoạch cá nhân, bận công việc..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Không, Quay Lại</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Xác Nhận Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
