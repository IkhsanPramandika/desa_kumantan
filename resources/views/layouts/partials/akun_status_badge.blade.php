@php
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
@endphp
<span class="badge badge-{{ $badgeClass }}"><i class="{{ $icon }} mr-1"></i> {{ $text }}</span>