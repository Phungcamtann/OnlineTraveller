<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle mb-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-compass text-warning fs-4"></i>
                    </span>
                    <h3 class="font-editorial fw-bold text-dark mb-1">Đăng Nhập</h3>
                    <p class="text-muted small">Chào mừng bạn quay lại với OnlineTraveller</p>
                </div>

                <form action="/login" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-0" placeholder="name@example.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-muted mb-0">Mật khẩu</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label text-muted" for="rememberMe">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-editorial w-100 py-2 fs-6 mb-3">
                        Đăng Nhập Ngay
                    </button>
                </form>

                <div class="text-center pt-3 border-top small text-muted">
                    Chưa có tài khoản? <a href="/register" class="text-primary fw-semibold text-decoration-none">Đăng ký ngay</a>
                </div>

                <!-- Fast Demo Login Credentials -->
                <div class="mt-4 p-3 bg-light rounded-3 border small">
                    <strong class="d-block mb-1 text-dark"><i class="bi bi-info-circle me-1"></i> Tài khoản mẫu thử nghiệm (Pass: password123):</strong>
                    <ul class="mb-0 ps-3 text-muted">
                        <li>Admin: <code>admin@onlinetraveller.vn</code></li>
                        <li>Host Đà Lạt: <code>host.dalat@onlinetraveller.vn</code></li>
                        <li>Tourist: <code>tourist@gmail.com</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
