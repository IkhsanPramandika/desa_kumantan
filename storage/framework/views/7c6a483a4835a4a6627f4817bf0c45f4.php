

<?php
    $badgeClass = '';
    $icon = '';
    $text = ucfirst($status);

    switch ($status) {
        case 'pending':
            $badgeClass = 'warning';
            $icon = 'fas fa-hourglass-half';
            break;
        case 'membutuhkan_revisi':
            $badgeClass = 'warning';
            $icon = 'fas fa-exclamation-circle';
            $text = 'Perlu Revisi';
            break;
        case 'diterima':
        case 'diproses':
            $badgeClass = 'info';
            $icon = 'fas fa-sync-alt';
            $text = 'Diproses';
            break;
        case 'selesai':
            $badgeClass = 'success';
            $icon = 'fas fa-check-circle';
            break;
        case 'ditolak':
            $badgeClass = 'danger';
            $icon = 'fas fa-times-circle';
            break;
        default:
            $badgeClass = 'secondary';
            $icon = 'fas fa-question-circle';
            break;
    }
?>

<span class="badge badge-<?php echo e($badgeClass); ?>">
    <i class="<?php echo e($icon); ?> mr-1"></i> <?php echo e($text); ?>

</span><?php /**PATH C:\PA\desa_kumantan\desa_kumantan\resources\views/layouts/partials/status_badge.blade.php ENDPATH**/ ?>