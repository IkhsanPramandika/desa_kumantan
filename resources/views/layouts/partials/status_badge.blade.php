{{-- Lokasi: resources/views/layouts/partials/status_badge.blade.php --}}

@php
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
@endphp

<span class="badge badge-{{ $badgeClass }}">
    <i class="{{ $icon }} mr-1"></i> {{ $text }}
</span>