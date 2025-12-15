<!--
    Holiday Management Component
    Displays the holiday management form and list
    Usage: @component('Presensi.components.holiday-management')
-->
@props(['stepNumber' => '3'])

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">{{ $stepNumber }}</span>
                <h5 class="mb-0">Hari Libur</h5>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="frmHoliday" class="mb-3">
            <div class="mb-2">
                <label class="form-label small fw-semibold">Tanggal</label>
                <input id="holidayDate" class="form-control form-control-sm" type="date">
            </div>
            <div class="mb-2">
                <label class="form-label small fw-semibold">Nama Libur</label>
                <input id="holidayName" class="form-control form-control-sm" placeholder="Contoh: Idul Fitri">
            </div>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="holidayNational" checked>
                <label class="form-check-label small" for="holidayNational">Libur Nasional</label>
            </div>
            <button id="btnAddHoliday" class="btn btn-success btn-sm w-100">
                <i class="fas fa-plus me-1"></i> Tambah Hari Libur
            </button>
        </form>
        
        <div class="table-responsive">
            <table class="table table-sm table-hover" id="tblHolidays">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>