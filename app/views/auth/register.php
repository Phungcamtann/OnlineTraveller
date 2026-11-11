<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-9">
            <div class="surface-card rounded-4 shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <img src="/assets/images/logo.svg" 
                             alt="OnlineTraveller" 
                             width="52" 
                             height="52" 
                             class="rounded-circle shadow-sm"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                        <span class="navbar-brand-logo mx-auto" style="width: 52px; height: 52px; display: none;">
                            <i class="bi bi-compass fs-3 text-info"></i>
                        </span>
                    </div>
                    <h3 class="font-editorial fw-bold mb-1" style="color: var(--text-main);">Tạo Tài Khoản Mới</h3>
                    <p class="small mb-0" style="color: var(--text-muted);">Tham gia cộng đồng du lịch từ xa và trải nghiệm bản địa</p>
                </div>

                <form action="/register" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--text-main);">Họ và Tên</label>
                        <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn A" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" style="color: var(--text-main);">Địa chỉ Email</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" style="color: var(--text-main);">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control" placeholder="0912345678">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--text-main);">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold" style="color: var(--text-main);">Mục đích tham gia</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleTourist" value="tourist" checked>
                                <label class="btn btn-outline-secondary w-100 text-start p-3 rounded-3" for="roleTourist">
                                    <div class="fw-bold" style="color: var(--text-main);"><i class="bi bi-backpack2 me-1 text-primary"></i> Du Khách</div>
                                    <small style="color: var(--text-muted); font-size: 0.75rem;">Xem 3D từ xa & đặt lịch tour</small>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleHost" value="host">
                                <label class="btn btn-outline-secondary w-100 text-start p-3 rounded-3" for="roleHost">
                                    <div class="fw-bold" style="color: var(--text-main);"><i class="bi bi-house-door me-1 text-success"></i> Dân Bản Địa</div>
                                    <small style="color: var(--text-muted); font-size: 0.75rem;">Đăng tour & đón tiếp Homestay</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-theme-primary w-100 py-2 fs-6 mb-3">
                        Hoàn Tất Đăng Ký
                    </button>
                </form>

                <div class="text-center pt-3 border-top border-subtle small" style="color: var(--text-muted);">
                    Đã có tài khoản? <a href="/login" class="text-info fw-semibold text-decoration-none">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>
