@extends('layouts.base')

@section('content')
<div class="page-content">
  <section class="row">
    <div class="col-12 col-lg-8">
      <div class="card">
        <div class="card-header"><h5>Presensi Saya</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Pilih Toko</label>
            <select id="storeSelect" class="form-select"></select>
          </div>
          <div class="mb-2 text-muted" id="geoStatus">Lokasi: belum diambil</div>
          <div class="d-flex gap-2">
            <button id="btnCheckIn" class="btn btn-success">Absen Masuk</button>
            <button id="btnCheckOut" class="btn btn-warning">Absen Keluar</button>
          </div>
          <hr>
          <div>
            <h6>Riwayat (60 hari)</h6>
            <div class="table-responsive">
              <table class="table table-sm" id="tblHistory">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Terlambat</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  const storeSelect = document.getElementById('storeSelect');
  const tblBody = document.querySelector('#tblHistory tbody');
  const geoStatus = document.getElementById('geoStatus');

  async function fetchStores() {
    const res = await fetch('/stores');
    const data = await res.json();
    storeSelect.innerHTML = data.map(s => `<option value="${s.id}">${s.name} (${s.latitude}, ${s.longitude})</option>`).join('');
  }

  async function fetchHistory() {
    const res = await fetch('/attendance/me');
    const list = await res.json();
    tblBody.innerHTML = list.map(r => `
      <tr>
        <td>${r.work_date}</td>
        <td>${r.check_in_at ?? '-'}</td>
        <td>${r.check_out_at ?? '-'}</td>
        <td>${r.is_late ? 'Ya' : 'Tidak'}</td>
        <td>${r.approval_status ?? '-' } ${r.flagged_mangkir ? '(mangkir?)' : ''}</td>
      </tr>
    `).join('');
  }

  function getPosition() {
    return new Promise((resolve, reject) => {
      if (!navigator.geolocation) return reject('Geolocation tidak didukung browser');
      navigator.geolocation.getCurrentPosition(pos => {
        resolve({lat: pos.coords.latitude, lng: pos.coords.longitude});
      }, err => reject(err.message), { enableHighAccuracy: true, timeout: 8000 });
    });
  }

  async function action(endpoint) {
    try {
      const store_id = storeSelect.value;
      if (!store_id) {
        Swal.fire({
          icon: 'warning',
          title: 'Peringatan',
          text: 'Pilih toko terlebih dahulu',
          toast: true,
          position: 'top-right',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
        return;
      }
      geoStatus.textContent = 'Mengambil lokasi...';
      const {lat, lng} = await getPosition();
      geoStatus.textContent = `Lokasi: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ store_id, lat, lng })
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Gagal');
      
      // Success notification
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: data.message || 'Presensi berhasil dicatat',
        toast: true,
        position: 'top-right',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
      
      fetchHistory();
    } catch (e) {
      // Error notification
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: e.message || e,
        toast: true,
        position: 'top-right',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true
      });
    }
  }

  document.getElementById('btnCheckIn').addEventListener('click', () => action('/attendance/check-in'));
  document.getElementById('btnCheckOut').addEventListener('click', () => action('/attendance/check-out'));

  fetchStores();
  fetchHistory();
</script>
@endsection

