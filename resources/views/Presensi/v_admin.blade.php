@extends('layouts.base')

@section('content')
<!-- External CSS Libraries -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- External JavaScript Libraries -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Dashboard Header -->
<section class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user-clock text-primary me-2"></i>
                    Manajemen Presensi
                </h2>
                <p class="text-muted mb-0">Kelola toko, pengaturan presensi, hari libur, dan approval keterlambatan</p>
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

<!-- Quick Guide Alert -->
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="fas fa-info-circle me-2 flex-shrink-0"></i>
    <div class="flex-grow-1">
        <strong>Alur cepat:</strong> 
        <span class="d-none d-md-inline">1) Pilih/tambah toko pada peta, 2) Atur jam presensi per toko, 3) Tambah hari libur, 4) Review approval keterlambatan.</span>
        <span class="d-md-none">Pilih toko → Atur jam → Tambah libur → Review approval.</span>
    </div>
</div>

<!-- Dashboard Summary Cards -->
<section class="row mb-4">
    <!-- Total Stores Card -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-store text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Toko</h6>
                        <h4 class="mb-0" id="totalStores">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Settings Card -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pengaturan Aktif</h6>
                        <h4 class="mb-0" id="activeSettings">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Holidays Card -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-times text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Hari Libur</h6>
                        <h4 class="mb-0" id="totalHolidays">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Approvals Card -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pending Approval</h6>
                        <h4 class="mb-0" id="pendingApprovals">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Area -->
<div class="row">
    <!-- Left Column: Map & Store Management -->
    <div class="col-lg-8 mb-4">
        <!-- Store Management Component -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">1</span>
                        <h5 class="mb-0">Toko & Geofence</h5>
                    </div>
                    <small class="text-muted">Klik peta untuk set titik</small>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-7">
                        <div class="position-relative">
                            <div id="map" style="height: 400px; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0;"></div>
                            <div class="position-absolute top-0 start-0 m-2 z-index-1000">
                                <div class="bg-white rounded shadow-sm p-2">
                                    <small class="text-muted d-block mb-1">Kontrol Peta</small>
                                    <div class="d-flex gap-1">
                                        <button id="btnMyLoc" class="btn btn-sm btn-outline-primary" title="Lokasi Saya">
                                            <i class="fas fa-crosshairs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="input-group">
                                <input id="searchBox" class="form-control" placeholder="Cari lokasi (jalan/kota)">
                                <button id="btnSearch" class="btn btn-outline-secondary">
                                    <i class="fas fa-search me-1"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <form id="frmStore" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-store me-1 text-primary"></i> Nama Toko
                                </label>
                                <input class="form-control" name="name" placeholder="Contoh: Gading Printing Pusat" required>
                                <div class="invalid-feedback">Nama toko harus diisi</div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i> Latitude
                                    </label>
                                    <input class="form-control" name="latitude" placeholder="Klik peta" required type="number" step="any">
                                    <div class="invalid-feedback">Latitude harus diisi</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i> Longitude
                                    </label>
                                    <input class="form-control" name="longitude" placeholder="Klik peta" required type="number" step="any">
                                    <div class="invalid-feedback">Longitude harus diisi</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-circle-notch me-1 text-info"></i> Radius Geofence
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="range" id="radiusRange" class="form-range flex-grow-1" min="10" max="1000" step="5" value="100">
                                    <span class="badge bg-light text-dark" style="min-width: 80px;">
                                        <span id="radiusVal">100</span> m
                                    </span>
                                </div>
                                <input class="form-control mt-2" name="radius_meters" type="number" min="10" value="100" placeholder="Radius dalam meter">
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-grow-1" type="submit">
                                    <i class="fas fa-save me-1"></i> Simpan Toko
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="btnResetForm">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Store List -->
                <div class="mt-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="fas fa-list me-1"></i> Daftar Toko Terdaftar
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tblStores">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-store me-1"></i> Nama</th>
                                    <th><i class="fas fa-map-pin me-1"></i> Koordinat</th>
                                    <th><i class="fas fa-circle-notch me-1"></i> Radius</th>
                                    <th><i class="fas fa-toggle-on me-1"></i> Status</th>
                                    <th><i class="fas fa-cog me-1"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Settings & Quick Actions -->
    <div class="col-lg-4 mb-4">
        <!-- Attendance Settings -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">2</span>
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

        <!-- Holiday Management -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">3</span>
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
    </div>
</div>

<!-- Approval Workflow -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">4</span>
                        <h5 class="mb-0">Approval Keterlambatan</h5>
                        <span class="badge bg-danger ms-2" id="pendingApprovalBadge">0</span>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadApprovals()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tblApprovals">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-calendar me-1"></i> Tanggal</th>
                                <th><i class="fas fa-user me-1"></i> Nama</th>
                                <th><i class="fas fa-clock me-1"></i> Jam Masuk</th>
                                <th><i class="fas fa-info-circle me-1"></i> Status</th>
                                <th><i class="fas fa-cogs me-1"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="noApprovalsMessage" class="text-center py-4 text-muted d-none">
                        <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                        <p>Tidak ada approval yang pending</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Admin Main JavaScript Component
 * Initializes all components and handles global functionality
 */

// Leaflet Map variables
let map, marker, circle;

// Global variables
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const $stores = document.querySelector('#tblStores tbody');
const $settingStore = document.getElementById('settingStore');

/**
 * Initialize the map with default settings
 */
function initMap() {
    map = L.map('map').setView([-6.200, 106.816], 12); // Jakarta default
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([-6.200, 106.816], { draggable: true }).addTo(map);
    circle = L.circle([-6.200, 106.816], { radius: 100, color: '#0d6efd' }).addTo(map);

    map.on('click', (e) => {
        setPoint(e.latlng.lat, e.latlng.lng);
    });
    marker.on('dragend', () => {
        const p = marker.getLatLng();
        setPoint(p.lat, p.lng, false);
    });
}

/**
 * Set the point on the map and update form fields
 * @param {number} lat - Latitude
 * @param {number} lng - Longitude
 * @param {boolean} moveMap - Whether to move the map view
 */
function setPoint(lat, lng, moveMap = true) {
    const f = document.getElementById('frmStore');
    f.latitude.value = lat.toFixed(7);
    f.longitude.value = lng.toFixed(7);
    marker.setLatLng([lat, lng]);
    circle.setLatLng([lat, lng]);
    if (moveMap) map.setView([lat, lng], 16);
}

/**
 * Set the radius for the geofence circle
 * @param {number} m - Radius in meters
 */
function setRadius(m) {
    document.getElementById('radiusVal').textContent = m;
    document.getElementById('frmStore').radius_meters.value = m;
    circle.setRadius(Number(m));
}

/**
 * Get user's current location
 */
async function getCurrentLocation() {
    if (!navigator.geolocation) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Geolocation tidak didukung browser',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
        setPoint(pos.coords.latitude, pos.coords.longitude);
    }, err => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.message,
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }, { enableHighAccuracy: true, timeout: 8000 });
}

/**
 * Search for a location using Nominatim API
 */
async function searchLocation() {
    const q = document.getElementById('searchBox').value.trim();
    if (!q) return;
    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=5`);
        const list = await res.json();
        if (!list.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Lokasi tidak ditemukan',
                toast: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            return;
        }
        const p = list[0];
        setPoint(parseFloat(p.lat), parseFloat(p.lon));
    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal mencari lokasi',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
}

/**
 * Load all stores and populate the table and dropdown
 */
async function loadStores() {
    const res = await fetch('/stores');
    const data = await res.json();
    $stores.innerHTML = data.map(s => `
      <tr>
        <td>
          <div class="d-flex align-items-center">
            <i class="fas fa-store me-2 text-primary"></i>
            <div>
              <div class="fw-semibold">${s.name}</div>
              <small class="text-muted">ID: ${s.id}</small>
            </div>
          </div>
        </td>
        <td>
          <small class="font-monospace">${s.latitude}, ${s.longitude}</small>
        </td>
        <td>
          <span class="badge bg-info">
            <i class="fas fa-circle-notch me-1"></i>${s.radius_meters} m
          </span>
        </td>
        <td>
          <span class="badge ${s.active ? 'bg-success' : 'bg-secondary'}">
            <i class="fas fa-${s.active ? 'check' : 'times'} me-1"></i>
            ${s.active ? 'Aktif' : 'Non-aktif'}
          </span>
        </td>
        <td>
          <button class="btn btn-sm btn-outline-primary" onclick='fillStore(${JSON.stringify(s).replaceAll("'", "'")})'>
            <i class="fas fa-edit me-1"></i> Edit
          </button>
        </td>
      </tr>`).join('');
    
    $settingStore.innerHTML = '<option value="">-- Pilih Toko --</option>' + 
      data.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    
    // Update dashboard counters
    document.getElementById('totalStores').textContent = data.length;
    document.getElementById('activeSettings').textContent = data.filter(s => s.active).length;
    
    if (data[0]) fillStore(data[0]);
}

/**
 * Fill the store form with store data
 * @param {Object} s - Store object
 */
function fillStore(s) {
    const f = document.getElementById('frmStore');
    f.name.value = s.name || '';
    f.latitude.value = s.latitude ?? '';
    f.longitude.value = s.longitude ?? '';
    f.radius_meters.value = s.radius_meters ?? 100;
    document.getElementById('radiusRange').value = s.radius_meters ?? 100;
    setRadius(s.radius_meters ?? 100);
    if (s.latitude && s.longitude) setPoint(parseFloat(s.latitude), parseFloat(s.longitude));
    f.dataset.id = s.id || '';
}

/**
 * Reset the store form
 */
function resetStoreForm() {
    const f = document.getElementById('frmStore');
    f.reset();
    f.dataset.id = '';
    setPoint(-6.200, 106.816);
    setRadius(100);
    document.getElementById('radiusRange').value = 100;
}

/**
 * Save store (create or update)
 */
async function saveStore(e) {
    e.preventDefault();
    if (!e.target.checkValidity()) {
        e.target.classList.add('was-validated');
        return;
    }
    
    const f = e.target;
    const body = {
      name: f.name.value,
      latitude: f.latitude.value,
      longitude: f.longitude.value,
      radius_meters: f.radius_meters.value,
    };
    const id = f.dataset.id;
    const url = id ? `/stores/${id}` : '/stores';
    const method = id ? 'PUT' : 'POST';
    const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body)});
    const data = await res.json();
    if (!res.ok) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: data.message || 'Gagal',
        toast: true,
        position: 'top-right',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
      return;
    }
    
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: 'Toko disimpan',
      toast: true,
      position: 'top-right',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
    
    f.dataset.id = data.data?.id || id || '';
    f.classList.remove('was-validated');
    loadStores();
}

/**
 * Load attendance settings for selected store
 */
async function loadSetting() {
    if (!$settingStore.value) return;
    const res = await fetch(`/stores/${$settingStore.value}/attendance-settings`);
    const s = await res.json();
    if (!s) return;
    document.getElementById('inOn').value = (s.check_in_on_time_until || '09:00:00').slice(0,5);
    document.getElementById('inLast').value = (s.check_in_last_allowed || '12:00:00').slice(0,5);
    document.getElementById('outEar').value = (s.check_out_earliest || '17:00:00').slice(0,5);
    document.getElementById('outLat').value = (s.check_out_latest || '23:59:00').slice(0,5);
    document.getElementById('enableWeekends').checked = !!s.enable_weekends;
}

/**
 * Save attendance settings for selected store
 */
async function saveAttendanceSettings() {
    if (!$settingStore.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih toko',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        return;
    }
    const body = {
        check_in_on_time_until: document.getElementById('inOn').value,
        check_in_last_allowed: document.getElementById('inLast').value,
        check_out_earliest: document.getElementById('outEar').value,
        check_out_latest: document.getElementById('outLat').value,
        enable_weekends: document.getElementById('enableWeekends').checked,
    };
    const res = await fetch(`/stores/${$settingStore.value}/attendance-settings`, { 
        method: 'POST', 
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, 
        body: JSON.stringify(body)
    });
    const data = await res.json();
    if (!res.ok) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Gagal',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        return;
    }
    
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Pengaturan disimpan',
        toast: true,
        position: 'top-right',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

/**
 * Load all holidays and populate the table
 */
async function loadHolidays() {
    const res = await fetch('/holidays');
    const list = await res.json();
    const tbody = document.querySelector('#tblHolidays tbody');
    tbody.innerHTML = list.map(h => `
      <tr>
        <td>
          <small class="font-monospace">${h.date}</small>
        </td>
        <td>
          <div>
            <div class="fw-semibold">${h.name}</div>
            ${h.is_national ? '<small class="text-muted"><i class="fas fa-flag me-1"></i>Nasional</small>' : ''}
          </div>
        </td>
        <td>
          <button class='btn btn-sm btn-outline-danger' onclick='confirmDelHoliday(${h.id}, "${h.name}")'>
            <i class="fas fa-trash me-1"></i> Hapus
          </button>
        </td>
      </tr>`).join('');
    
    // Update dashboard counter
    document.getElementById('totalHolidays').textContent = list.length;
}

/**
 * Add a new holiday
 */
async function addHoliday() {
    const date = document.getElementById('holidayDate').value;
    const name = document.getElementById('holidayName').value;
    const is_national = document.getElementById('holidayNational').checked;
    
    if (!date || !name) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Tanggal dan nama libur harus diisi',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        return;
    }
    
    const res = await fetch('/holidays', { 
        method: 'POST', 
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, 
        body: JSON.stringify({ date, name, is_national })
    });
    const data = await res.json();
    if (!res.ok) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Gagal',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        return;
    }
    
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Hari libur ditambahkan',
        toast: true,
        position: 'top-right',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    
    document.getElementById('holidayDate').value = '';
    document.getElementById('holidayName').value = '';
    loadHolidays();
}

/**
 * Delete a holiday
 * @param {number} id - Holiday ID
 */
async function delHoliday(id) {
    const res = await fetch(`/holidays/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf }});
    if (res.ok) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Hari libur dihapus',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        loadHolidays();
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal hapus',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
}

/**
 * Confirm deletion of a holiday
 * @param {number} id - Holiday ID
 * @param {string} name - Holiday name
 */
function confirmDelHoliday(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus hari libur "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            delHoliday(id);
        }
    });
}

/**
 * Load all pending attendance approvals
 */
async function loadApprovals() {
    const res = await fetch('/attendance/approvals/pending');
    const list = await res.json();
    const tbody = document.querySelector('#tblApprovals tbody');
    const noMessage = document.getElementById('noApprovalsMessage');
    
    if (list.length === 0) {
        tbody.innerHTML = '';
        noMessage.classList.remove('d-none');
    } else {
        noMessage.classList.add('d-none');
        tbody.innerHTML = list.map(a => `
        <tr>
          <td>
            <div class="d-flex align-items-center">
              <i class="fas fa-calendar-alt me-2 text-primary"></i>
              <div>
                <div class="fw-semibold">${a.attendance?.work_date ?? '-'}</div>
                <small class="text-muted">ID: ${a.id}</small>
              </div>
            </div>
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="bg-light rounded-circle p-2 me-2">
                <i class="fas fa-user text-muted"></i>
              </div>
              <div class="fw-semibold">${a.requester?.name ?? '-'}</div>
            </div>
          </td>
          <td>
            <span class="badge bg-warning">
              <i class="fas fa-clock me-1"></i>
              ${a.attendance?.check_in_at ?? '-'}
            </span>
          </td>
          <td>
            <span class="badge bg-secondary">${a.status}</span>
          </td>
          <td>
            <div class="btn-group" role="group">
              <button class='btn btn-sm btn-success' onclick='confirmReviewApproval(${a.id}, true, "${a.requester?.name ?? ''}", "${a.attendance?.work_date ?? ''}")'>
                <i class="fas fa-check me-1"></i> Approve
              </button>
              <button class='btn btn-sm btn-danger' onclick='confirmReviewApproval(${a.id}, false, "${a.requester?.name ?? ''}", "${a.attendance?.work_date ?? ''}")'>
                <i class="fas fa-times me-1"></i> Reject
              </button>
            </div>
          </td>
        </tr>`).join('');
    }
    
    // Update dashboard counter
    document.getElementById('pendingApprovals').textContent = list.length;
    document.getElementById('pendingApprovalBadge').textContent = list.length;
}

/**
 * Review an attendance approval (approve or reject)
 * @param {number} id - Approval ID
 * @param {boolean} approve - Whether to approve (true) or reject (false)
 */
async function reviewApproval(id, approve) {
    const url = approve ? `/attendance/approvals/${id}/approve` : `/attendance/approvals/${id}/reject`;
    const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }});
    const data = await res.json();
    if (!res.ok) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Gagal',
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        return;
    }
    
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: approve ? 'Approval disetujui' : 'Approval ditolak',
        toast: true,
        position: 'top-right',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    
    loadApprovals();
}

/**
 * Confirm review of an attendance approval
 * @param {number} id - Approval ID
 * @param {boolean} approve - Whether to approve (true) or reject (false)
 * @param {string} name - Employee name
 * @param {string} date - Attendance date
 */
function confirmReviewApproval(id, approve, name, date) {
    const action = approve ? 'menyetujui' : 'menolak';
    const title = approve ? 'Konfirmasi Persetujuan' : 'Konfirmasi Penolakan';
    const text = `Apakah Anda yakin ingin ${action} presensi ${name} pada tanggal ${date}?`;
    const confirmButtonText = approve ? 'Ya, Setujui' : 'Ya, Tolak';
    
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: approve ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            reviewApproval(id, approve);
        }
    });
}

/**
 * Initialize all components when DOM is ready
 */
async function initializeComponents() {
    // Initialize map first
    if (typeof initMap === 'function') {
        initMap();
    }
    
    // Load all data
    if (typeof loadStores === 'function') {
        await loadStores();
    }
    
    if (typeof loadSetting === 'function') {
        await loadSetting();
    }
    
    if (typeof loadHolidays === 'function') {
        await loadHolidays();
    }
    
    if (typeof loadApprovals === 'function') {
        await loadApprovals();
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Map controls
    document.getElementById('radiusRange').addEventListener('input', (e) => setRadius(e.target.value));
    document.getElementById('frmStore').radius_meters.addEventListener('input', (e) => setRadius(e.target.value));
    document.getElementById('btnMyLoc').addEventListener('click', getCurrentLocation);
    document.getElementById('btnSearch').addEventListener('click', searchLocation);
    
    // Store management
    document.getElementById('frmStore').addEventListener('submit', saveStore);
    document.getElementById('btnResetForm').addEventListener('click', resetStoreForm);
    
    // Attendance settings
    $settingStore.addEventListener('change', loadSetting);
    document.getElementById('btnSaveSetting').addEventListener('click', saveAttendanceSettings);
    
    // Holiday management
    document.getElementById('btnAddHoliday').addEventListener('click', addHoliday);
    
    // Initialize all components
    initializeComponents();
});

// Make functions globally accessible
window.loadStores = loadStores;
window.fillStore = fillStore;
window.resetStoreForm = resetStoreForm;
window.loadSetting = loadSetting;
window.saveAttendanceSettings = saveAttendanceSettings;
window.loadHolidays = loadHolidays;
window.delHoliday = delHoliday;
window.confirmDelHoliday = confirmDelHoliday;
window.loadApprovals = loadApprovals;
window.reviewApproval = reviewApproval;
window.confirmReviewApproval = confirmReviewApproval;
window.setPoint = setPoint;
window.setRadius = setRadius;
window.getCurrentLocation = getCurrentLocation;
window.searchLocation = searchLocation;
window.saveStore = saveStore;
window.addHoliday = addHoliday;
</script>
@endsection
