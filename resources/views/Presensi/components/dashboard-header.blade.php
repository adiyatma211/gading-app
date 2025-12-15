<!--
    Dashboard Header Component
    Displays the main header with title, description, and current date
    Usage: @component('Presensi.components.dashboard-header')
-->
@props(['title' => 'Manajemen Presensi', 'description' => 'Kelola toko, pengaturan presensi, hari libur, dan approval keterlambatan'])

<section class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-clock text-primary me-2"></i>
                    {{ $title }}
                </h2>
                <p class="text-muted mb-0">{{ $description }}</p>
            </div>
            <div class="d-none d-md-block">
                <span class="badge bg-light text-dark">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ now()->format('l, d F Y') }}
                </span>
            </div>
        </div>
    </div>
</section>