<!-- REUSABLE FLOATING ALERT TOASTS CONTAINER (ĐỒNG BỘ THEME DAY / NIGHT CHO AJAX) -->
<div id="appToastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090; pointer-events: none;">
    <!-- Các Toast sinh động qua window.showToast() sẽ xuất hiện tại đây -->
</div>

<style>
/* Toast Container & Cards */
#appToastContainer > .app-toast {
    pointer-events: auto;
    width: clamp(300px, 90vw, 380px);
    background: var(--bg-surface, #ffffff);
    color: var(--text-main, #18181b);
    border: 1px solid var(--border-subtle, rgba(24, 24, 27, 0.1));
    border-radius: 18px;
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.12);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    margin-bottom: 12px;
    overflow: hidden;
    position: relative;
    opacity: 0;
    transform: translateX(60px);
    transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

[data-theme="dark"] #appToastContainer > .app-toast {
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
}

#appToastContainer > .app-toast.show {
    opacity: 1;
    transform: translateX(0);
}

#appToastContainer > .app-toast.hide {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
}

/* Toast Progress Bar */
.app-toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    width: 100%;
    transform-origin: left;
    animation: toastProgress linear forwards;
}

@keyframes toastProgress {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}

/* Biến thể màu Toast */
.app-toast-success .toast-icon-wrap {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}
.app-toast-success .app-toast-progress {
    background: #10b981;
}

.app-toast-danger .toast-icon-wrap,
.app-toast-error .toast-icon-wrap {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}
.app-toast-danger .app-toast-progress,
.app-toast-error .app-toast-progress {
    background: #ef4444;
}

.app-toast-warning .toast-icon-wrap {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
}
.app-toast-warning .app-toast-progress {
    background: #f59e0b;
}

.app-toast-info .toast-icon-wrap {
    background: rgba(2, 132, 199, 0.15);
    color: #0284c7;
}
.app-toast-info .app-toast-progress {
    background: #0284c7;
}
</style>

<script>
/**
 * Global AJAX Alert Toast Notification API
 * Cách dùng:
 *   window.showToast('Lưu dữ liệu thành công!', 'success');
 *   window.showToast('Có lỗi xảy ra khi gọi AJAX', 'error', 'Thất bại');
 *   window.showToast('Vui lòng hoàn tất thông tin', 'warning', 'Cảnh báo', 4000);
 */
window.showToast = function(message, type = 'success', title = null, duration = 3500) {
    const container = document.getElementById('appToastContainer');
    if (!container) return;

    // Chuẩn hóa loại toast
    if (type === 'danger') type = 'error';

    // Xác định icon và tiêu đề mặc định
    let iconClass = 'bi-check-circle-fill';
    let defaultTitle = 'Thành công';
    if (type === 'error') {
        iconClass = 'bi-x-circle-fill';
        defaultTitle = 'Đã có lỗi xảy ra';
    } else if (type === 'warning') {
        iconClass = 'bi-exclamation-triangle-fill';
        defaultTitle = 'Cảnh báo';
    } else if (type === 'info') {
        iconClass = 'bi-info-circle-fill';
        defaultTitle = 'Thông báo';
    }

    const displayTitle = title || defaultTitle;

    // Tạo phần tử Toast HTML
    const toastEl = document.createElement('div');
    toastEl.className = `app-toast app-toast-${type} p-3 d-flex align-items-start gap-3`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
        <div class="toast-icon-wrap d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; font-size: 1.25rem;">
            <i class="bi ${iconClass}"></i>
        </div>
        <div class="flex-grow-1 pe-2" style="min-width: 0;">
            <div class="fw-bold small mb-1" style="color: var(--text-main); font-family: var(--font-editorial, inherit);">${escapeHtml(displayTitle)}</div>
            <div class="small" style="color: var(--text-muted); line-height: 1.45; word-break: break-word;">${message}</div>
        </div>
        <button type="button" class="btn-close btn-close-sm flex-shrink-0" aria-label="Đóng" style="font-size: 0.75rem; opacity: 0.6;"></button>
        <div class="app-toast-progress" style="animation-duration: ${duration}ms;"></div>
    `;

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    container.appendChild(toastEl);

    // Kích hoạt animation slide-in
    requestAnimationFrame(() => {
        toastEl.classList.add('show');
    });

    let dismissTimer = null;

    function removeToast() {
        if (dismissTimer) clearTimeout(dismissTimer);
        toastEl.classList.remove('show');
        toastEl.classList.add('hide');
        setTimeout(() => {
            if (toastEl.parentNode === container) {
                container.removeChild(toastEl);
            }
        }, 300);
    }

    // Nút đóng thủ công
    const closeBtn = toastEl.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', removeToast);
    }

    // Tự động đóng sau duration
    if (duration > 0) {
        dismissTimer = setTimeout(removeToast, duration);
    }

    // Tạm dừng khi rê chuột vào
    toastEl.addEventListener('mouseenter', () => {
        if (dismissTimer) clearTimeout(dismissTimer);
        const progress = toastEl.querySelector('.app-toast-progress');
        if (progress) progress.style.animationPlayState = 'paused';
    });

    toastEl.addEventListener('mouseleave', () => {
        const progress = toastEl.querySelector('.app-toast-progress');
        if (progress) progress.style.animationPlayState = 'running';
        dismissTimer = setTimeout(removeToast, 1500);
    });

    return toastEl;
};

// Bí danh tương thích
window.showAppToast = window.showToast;
</script>
