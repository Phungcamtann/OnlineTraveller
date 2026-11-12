<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
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
                    <h3 class="font-editorial fw-bold mb-1" style="color: var(--text-main);">Đăng Nhập</h3>
                    <p class="small mb-0" style="color: var(--text-muted);">Chào mừng bạn quay lại với OnlineTraveller</p>
                </div>

                <form action="/login" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold" style="color: var(--text-main);">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope text-info"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0" style="color: var(--text-main);">Mật khẩu</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock text-info"></i></span>
                            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="••••••••" required>
                            <button type="button" class="btn btn-outline-secondary border-start-0" id="btnTogglePassword" title="Hiện/ẩn mật khẩu" style="border-top-right-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">
                                <i class="bi bi-eye" id="iconEye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" style="color: var(--text-muted);" for="rememberMe">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-theme-primary w-100 py-2 fs-6 mb-3">
                        Đăng Nhập Ngay
                    </button>
                </form>

                <div class="text-center pt-3 border-top border-subtle small" style="color: var(--text-muted);">
                    Chưa có tài khoản? <a href="/register" class="text-info fw-semibold text-decoration-none">Đăng ký ngay</a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnToggle = document.getElementById('btnTogglePassword');
    const inputPass = document.getElementById('inputPassword');
    const iconEye = document.getElementById('iconEye');

    if (btnToggle && inputPass && iconEye) {
        btnToggle.addEventListener('click', function() {
            const isPassword = inputPass.getAttribute('type') === 'password';
            inputPass.setAttribute('type', isPassword ? 'text' : 'password');
            if (isPassword) {
                iconEye.classList.remove('bi-eye');
                iconEye.classList.add('bi-eye-slash');
                btnToggle.setAttribute('title', 'Ẩn mật khẩu');
            } else {
                iconEye.classList.remove('bi-eye-slash');
                iconEye.classList.add('bi-eye');
                btnToggle.setAttribute('title', 'Hiện mật khẩu');
            }
        });
    }
});
</script>
