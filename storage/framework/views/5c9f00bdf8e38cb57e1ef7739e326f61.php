<?php
    $badgeClass = ''; $icon = ''; $text = '';
    switch ($status) {
        case 'pending_verification':
            $badgeClass = 'warning'; $icon = 'fas fa-clock'; $text = 'Pending Verifikasi'; break;
        case 'active':
            $badgeClass = 'success'; $icon = 'fas fa-check-circle'; $text = 'Aktif'; break;
        case 'inactive':
            $badgeClass = 'secondary'; $icon = 'fas fa-user-slash'; $text = 'Tidak Aktif'; break;
        case 'rejected':
            $badgeClass = 'danger'; $icon = 'fas fa-times-circle'; $text = 'Ditolak'; break;
        default:
            $badgeClass = 'light'; $icon = 'fas fa-question-circle'; $text = 'Unknown'; break;
    }
?>
<span class="badge badge-<?php echo e($badgeClass); ?>"><i class="<?php echo e($icon); ?> mr-1"></i> <?php echo e($text); ?></span><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/layouts/partials/akun_status_badge.blade.php ENDPATH**/ ?>