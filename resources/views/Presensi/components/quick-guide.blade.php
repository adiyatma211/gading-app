<!--
    Quick Guide Component
    Displays a quick guide alert with workflow instructions
    Usage: @component('Presensi.components.quick-guide')
-->
@props(['fullText' => '1) Pilih/tambah toko pada peta, 2) Atur jam presensi per toko, 3) Tambah hari libur, 4) Review approval keterlambatan.', 
        'shortText' => 'Pilih toko → Atur jam → Tambah libur → Review approval.'])

<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="fas fa-info-circle me-2 flex-shrink-0"></i>
    <div class="flex-grow-1">
        <strong>Alur cepat:</strong> 
        <span class="d-none d-md-inline">{{ $fullText }}</span>
        <span class="d-md-none">{{ $shortText }}</span>
    </div>
</div>