<!--
    Attendance Settings Component
    Displays the attendance settings configuration form
    Usage: @component('Presensi.components.attendance-settings')
-->
@props(['stepNumber' => '2'])

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">{{ $stepNumber }}</span>
                <h5 class="mb-0">Pengaturan Presensi</h5>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-store me-1"></i> Pilih Toko
            </label>
            <select id="settingStore" class="form-select">
                <option value="">-- Pilih Toko --</option>
            </select>
        </div>
        
        <div class="alert alert-light border-start border-4 border-info mb-3">
            <small class="d-block text-muted mb-1">Jam Presensi</small>
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label small">On-time ≤</label>
                    <input id="inOn" class="form-control form-control-sm" type="time" value="09:00">
                    <div class="form-text">Di atas ini = terlambat</div>
                </div>
                <div class="col-6">
                    <label class="form-label small">Last In ≤</label>
                    <input id="inLast" class="form-control form-control-sm" type="time" value="12:00">
                    <div class="form-text">Batas maksimal</div>
                </div>
                <div class="col-6">
                    <label class="form-label small">Earliest Out ≥</label>
                    <input id="outEar" class="form-control form-control-sm" type="time" value="17:00">
                </div>
                <div class="col-6">
                    <label class="form-label small">Latest Out ≤</label>
                    <input id="outLat" class="form-control form-control-sm" type="time" value="23:59">
                </div>
            </div>
        </div>
        
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="enableWeekends" checked>
            <label class="form-check-label" for="enableWeekends">
                <i class="fas fa-calendar-week me-1"></i> Aktifkan presensi Sabtu-Minggu
            </label>
        </div>
        
        <button id="btnSaveSetting" class="btn btn-primary w-100">
            <i class="fas fa-save me-1"></i> Simpan Pengaturan
        </button>
    </div>
</div>