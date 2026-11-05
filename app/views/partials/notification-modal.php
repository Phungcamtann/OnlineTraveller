<?php
$hasFlash = !empty($_SESSION['flash']);
$flashType = 'info';
$flashTitle = 'Thông báo hệ thống';
$flashMessage = '';

if ($hasFlash) {
    foreach ($_SESSION['flash'] as $type => $messages) {
        $flashType = $type;
        $flashTitle = match($type) {
            'success' => 'Thao tác thành công',
            'danger', 'error' => 'Đã có lỗi xảy ra',
            'warning' => 'Cảnh báo',
            default => 'Thông báo hệ thống',
        };
        $msgList = (array)$messages;
        $flashMessage = implode('<br>', array_map(fn($m) => htmlspecialchars((string)$m, ENT_QUOTES, 'UTF-8'), $msgList));
        break;
    }
    unset($_SESSION['flash']);
}
?>

<!-- REUSABLE NOTIFICATION POP-UP MODAL -->
<div class="modal fade" id="appNotificationModal" tabindex="-1" aria-labelledby="appNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 rounded-4 shadow-lg p-3 text-center">
            <div class="modal-body py-3">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" id="appNotificationIconCircle" style="width: 68px; height: 68px;">
                        <i class="bi fs-1" id="appNotificationIcon"></i>
                    </span>
                </div>
                <h5 class="fw-bold font-editorial text-dark mb-2" id="appNotificationModalLabel">
                    <?= $flashTitle ?>
                </h5>
                <p class="text-muted small mb-4" id="appNotificationMessage">
                    <?= $flashMessage ?>
                </p>
                <button type="button" class="btn btn-editorial rounded-pill px-4 py-2 w-100" data-bs-dismiss="modal">
                    Đồng ý
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Global reusable notification pop-up.
 * Can be called anywhere via:
 * window.showAppNotification('Tiêu đề', 'Nội dung thông báo', 'success'|'danger'|'warning'|'info')
 */
window.showAppNotification = function(title, message, type = 'success') {
    const modalEl = document.getElementById('appNotificationModal');
    if (!modalEl) return;

    const label = document.getElementById('appNotificationModalLabel');
    const msg = document.getElementById('appNotificationMessage');
    const circle = document.getElementById('appNotificationIconCircle');
    const icon = document.getElementById('appNotificationIcon');

    if (label) label.textContent = title;
    if (msg) msg.innerHTML = message;

    if (circle && icon) {
        circle.className = 'd-inline-flex align-items-center justify-content-center rounded-circle';
        icon.className = 'bi fs-1';

        switch (type) {
            case 'success':
                circle.classList.add('bg-success-subtle', 'text-success');
                icon.classList.add('bi-check-circle-fill');
                break;
            case 'danger':
            case 'error':
                circle.classList.add('bg-danger-subtle', 'text-danger');
                icon.classList.add('bi-x-circle-fill');
                break;
            case 'warning':
                circle.classList.add('bg-warning-subtle', 'text-warning');
                icon.classList.add('bi-exclamation-triangle-fill');
                break;
            default:
                circle.classList.add('bg-primary-subtle', 'text-primary');
                icon.classList.add('bi-info-circle-fill');
                break;
        }
    }

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
};

<?php if ($hasFlash): ?>
document.addEventListener('DOMContentLoaded', function () {
    window.showAppNotification(
        <?= json_encode($flashTitle, JSON_UNESCAPED_UNICODE) ?>,
        <?= json_encode($flashMessage, JSON_UNESCAPED_UNICODE) ?>,
        <?= json_encode($flashType, JSON_UNESCAPED_UNICODE) ?>
    );
});
<?php endif; ?>
</script>
