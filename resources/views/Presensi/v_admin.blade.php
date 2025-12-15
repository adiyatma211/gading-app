@extends('layouts.base')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="page-content">
  <section class="row">
    <div class="col-12">
      <div class="alert alert-info mb-3">
        <strong>Alur cepat:</strong> 1) Pilih/tambah toko pada peta, 2) Atur jam presensi per toko, 3) Tambah hari libur, 4) Review approval keterlambatan.
      </div>

      <!-- STEP 1: TOKO & GEOFENCE -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">1) Toko & Geofence (klik peta untuk set titik)</h5></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-lg-6">
              <div id="map" style="height: 360px; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0;"></div>
              <div class="d-flex gap-2 mt-2">
                <input id="searchBox" class="form-control" placeholder="Cari lokasi (jalan/kota)">
                <button id="btnSearch" class="btn btn-outline-secondary">Cari</button>
                <button id="btnMyLoc" class="btn btn-outline-primary">Lokasi Saya</button>
              </div>
            </div>
            <div class="col-lg-6">
              <form id="frmStore" class="row g-3">
                <div class="col-12">
                  <label class="form-label">Nama Toko</label>
                  <input class="form-control" name="name" placeholder="Contoh: Gading Printing Pusat" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Latitude</label>
                  <input class="form-control" name="latitude" placeholder="Klik peta untuk mengisi" required type="number" step="any">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Longitude</label>
                  <input class="form-control" name="longitude" placeholder="Klik peta untuk mengisi" required type="number" step="any">
                </div>
                <div class="col-12">
                  <label class="form-label">Radius Geofence (meter)</label>
                  <input type="range" id="radiusRange" class="form-range" min="10" max="1000" step="5" value="100">
                  <div class="small text-muted">Radius: <span id="radiusVal">100</span> m</div>
                  <input class="form-control mt-2" name="radius_meters" type="number" min="10" value="100">
                </div>
                <div class="col-12">
                  <button class="btn btn-primary" type="submit">Simpan/Update Toko</button>
                  <button class="btn btn-secondary" type="button" id="btnResetForm">Reset</button>
                </div>
              </form>
            </div>
          </div>
          <hr>
          <div class="table-responsive">
            <table class="table table-sm align-middle" id="tblStores">
              <thead><tr><th>Nama</th><th>Koordinat</th><th>Radius</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- STEP 2: PENGATURAN WAKTU -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">2) Pengaturan Presensi per Toko</h5></div>
        <div class="card-body">
          <div class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Pilih Toko</label>
              <select id="settingStore" class="form-select"></select>
            </div>
            <div class="col-md-2">
              <label class="form-label">On-time ≤</label>
              <input id="inOn" class="form-control" type="time" value="09:00">
              <div class="form-text">Di atas ini = terlambat</div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Last In ≤</label>
              <input id="inLast" class="form-control" type="time" value="12:00">
              <div class="form-text">Batas maksimal check-in</div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Earliest Out ≥</label>
              <input id="outEar" class="form-control" type="time" value="17:00">
            </div>
            <div class="col-md-2">
              <label class="form-label">Latest Out ≤</label>
              <input id="outLat" class="form-control" type="time" value="23:59">
            </div>
          </div>
          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" id="enableWeekends" checked>
            <label class="form-check-label" for="enableWeekends">Aktifkan presensi Sabtu-Minggu</label>
          </div>
          <button id="btnSaveSetting" class="btn btn-primary mt-2">Simpan Pengaturan</button>
        </div>
      </div>

      <!-- STEP 3: HARI LIBUR -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">3) Hari Libur / Tanggal Merah</h5></div>
        <div class="card-body">
          <div class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label">Tanggal</label><input id="holidayDate" class="form-control" type="date"></div>
            <div class="col-md-5"><label class="form-label">Nama Libur</label><input id="holidayName" class="form-control" placeholder="Contoh: Idul Fitri"></div>
            <div class="col-md-2 form-check form-switch mt-4">
              <input class="form-check-input" type="checkbox" id="holidayNational" checked>
              <label class="form-check-label" for="holidayNational">Nasional</label>
            </div>
            <div class="col-md-2"><button id="btnAddHoliday" class="btn btn-success w-100 mt-4">Tambah</button></div>
          </div>
          <div class="table-responsive mt-3">
            <table class="table table-sm" id="tblHolidays">
              <thead><tr><th>Tanggal</th><th>Nama</th><th>Nasional</th><th>Aksi</th></tr></thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- STEP 4: APPROVAL -->
      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">4) Approval Keterlambatan</h5></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm" id="tblApprovals">
              <thead><tr><th>Tanggal</th><th>Nama</th><th>Masuk</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const $stores = document.querySelector('#tblStores tbody');
  const $settingStore = document.getElementById('settingStore');

  // Leaflet Map init
  let map, marker, circle;
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

  function setPoint(lat, lng, moveMap = true) {
    const f = document.getElementById('frmStore');
    f.latitude.value = lat.toFixed(7);
    f.longitude.value = lng.toFixed(7);
    marker.setLatLng([lat, lng]);
    circle.setLatLng([lat, lng]);
    if (moveMap) map.setView([lat, lng], 16);
  }

  function setRadius(m) {
    document.getElementById('radiusVal').textContent = m;
    document.getElementById('frmStore').radius_meters.value = m;
    circle.setRadius(Number(m));
  }

  document.getElementById('radiusRange').addEventListener('input', (e) => setRadius(e.target.value));
  document.getElementById('frmStore').radius_meters.addEventListener('input', (e) => setRadius(e.target.value));

  document.getElementById('btnMyLoc').addEventListener('click', async () => {
    if (!navigator.geolocation) return alert('Geolocation tidak didukung browser');
    navigator.geolocation.getCurrentPosition(pos => {
      setPoint(pos.coords.latitude, pos.coords.longitude);
    }, err => alert(err.message), { enableHighAccuracy: true, timeout: 8000 });
  });

  document.getElementById('btnSearch').addEventListener('click', async () => {
    const q = document.getElementById('searchBox').value.trim();
    if (!q) return;
    try {
      const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=5`);
      const list = await res.json();
      if (!list.length) return alert('Lokasi tidak ditemukan');
      const p = list[0];
      setPoint(parseFloat(p.lat), parseFloat(p.lon));
    } catch (e) {
      alert('Gagal mencari lokasi');
    }
  });

  async function loadStores() {
    const res = await fetch('/stores');
    const data = await res.json();
    $stores.innerHTML = data.map(s => `
      <tr>
        <td>${s.name}</td>
        <td>${s.latitude}, ${s.longitude}</td>
        <td>${s.radius_meters} m</td>
        <td>${s.active ? 'Aktif' : 'Non-aktif'}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary" onclick='fillStore(${JSON.stringify(s).replaceAll("'", "&#39;")})'>Edit</button>
        </td>
      </tr>`).join('');
    $settingStore.innerHTML = data.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    if (data[0]) fillStore(data[0]);
  }

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

  document.getElementById('btnResetForm').addEventListener('click', () => {
    const f = document.getElementById('frmStore');
    f.reset();
    f.dataset.id = '';
    setPoint(-6.200, 106.816);
    setRadius(100);
    document.getElementById('radiusRange').value = 100;
  });

  document.getElementById('frmStore').addEventListener('submit', async (e) => {
    e.preventDefault();
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
    if (!res.ok) return alert(data.message || 'Gagal');
    alert('Toko disimpan');
    f.dataset.id = data.data?.id || id || '';
    loadStores();
  });

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
  $settingStore.addEventListener('change', loadSetting);
  document.getElementById('btnSaveSetting').addEventListener('click', async () => {
    if (!$settingStore.value) return alert('Pilih toko');
    const body = {
      check_in_on_time_until: document.getElementById('inOn').value,
      check_in_last_allowed: document.getElementById('inLast').value,
      check_out_earliest: document.getElementById('outEar').value,
      check_out_latest: document.getElementById('outLat').value,
      enable_weekends: document.getElementById('enableWeekends').checked,
    };
    const res = await fetch(`/stores/${$settingStore.value}/attendance-settings`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body)});
    const data = await res.json();
    if (!res.ok) return alert(data.message || 'Gagal');
    alert('Pengaturan disimpan');
  });

  async function loadHolidays() {
    const res = await fetch('/holidays');
    const list = await res.json();
    const tbody = document.querySelector('#tblHolidays tbody');
    tbody.innerHTML = list.map(h => `
      <tr>
        <td>${h.date}</td><td>${h.name}</td><td>${h.is_national ? 'Ya' : 'Tidak'}</td>
        <td><button class='btn btn-sm btn-outline-danger' onclick='delHoliday(${h.id})'>Hapus</button></td>
      </tr>`).join('');
  }
  document.getElementById('btnAddHoliday').addEventListener('click', async () => {
    const date = document.getElementById('holidayDate').value;
    const name = document.getElementById('holidayName').value;
    const is_national = document.getElementById('holidayNational').checked;
    const res = await fetch('/holidays', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ date, name, is_national })});
    const data = await res.json();
    if (!res.ok) return alert(data.message || 'Gagal');
    document.getElementById('holidayDate').value = '';
    document.getElementById('holidayName').value = '';
    loadHolidays();
  });
  async function delHoliday(id) {
    const res = await fetch(`/holidays/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf }});
    if (res.ok) loadHolidays(); else alert('Gagal hapus');
  }
  window.delHoliday = delHoliday;

  async function loadApprovals() {
    const res = await fetch('/attendance/approvals/pending');
    const list = await res.json();
    const tbody = document.querySelector('#tblApprovals tbody');
    tbody.innerHTML = list.map(a => `
      <tr>
        <td>${a.attendance?.work_date ?? '-'}</td>
        <td>${a.requester?.name ?? '-'}</td>
        <td>${a.attendance?.check_in_at ?? '-'}</td>
        <td>${a.status}</td>
        <td>
          <button class='btn btn-sm btn-success' onclick='reviewApproval(${a.id}, true)'>Approve</button>
          <button class='btn btn-sm btn-danger' onclick='reviewApproval(${a.id}, false)'>Reject</button>
        </td>
      </tr>`).join('');
  }
  async function reviewApproval(id, approve) {
    const url = approve ? `/attendance/approvals/${id}/approve` : `/attendance/approvals/${id}/reject`;
    const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }});
    const data = await res.json();
    if (!res.ok) return alert(data.message || 'Gagal');
    loadApprovals();
  }
  window.reviewApproval = reviewApproval;

  (async function init(){
    initMap();
    await loadStores();
    await loadSetting();
    await loadHolidays();
    await loadApprovals();
  })();
</script>
@endsection
