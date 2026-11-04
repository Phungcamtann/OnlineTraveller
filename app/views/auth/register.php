<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-9">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle mb-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-plus text-warning fs-4"></i>
                    </span>
                    <h3 class="font-editorial fw-bold text-dark mb-1">Tạo Tài Khoản Mới</h3>
                    <p class="text-muted small">Tham gia cộng đồng du lịch từ xa và trải nghiệm bản địa</p>
                </div>

                <form action="/register" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Họ và Tên</label>
                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="Nguyễn Văn A" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Địa chỉ Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0" placeholder="name@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control bg-light border-0" placeholder="0912345678">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mật khẩu</label>
                        <input type="password" name="password" class="form-control bg-light border-0" placeholder="Tối thiểu 6 ký tự" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Mục đích tham gia</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleTourist" value="tourist" checked>
                                <label class="btn btn-outline-secondary w-100 text-start p-3 rounded-3" for="roleTourist">
                                    <div class="fw-bold text-dark"><i class="bi bi-backpack2 me-1 text-primary"></i> Du Khách</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Xem 3D từ xa & đặt lịch tour</small>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleHost" value="host">
                                <label class="btn btn-outline-secondary w-100 text-start p-3 rounded-3" for="roleHost">
                                    <div class="fw-bold text-dark"><i class="bi bi-house-door me-1 text-success"></i> Dân Bản Địa</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Đăng tour & đón tiếp Homestay</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-editorial w-100 py-2 fs-6 mb-3">
                        Hoàn Tất Đăng Ký
                    </button>
                </form>

                <div class="text-center pt-3 border-top small text-muted">
                    Đã có tài khoản? <a href="/login" class="text-primary fw-semibold text-decoration-none">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>
