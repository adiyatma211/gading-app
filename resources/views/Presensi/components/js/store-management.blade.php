/**
 * Store Management JavaScript Component
 * Handles store CRUD operations and form management
 */

const csrf = document.querySelector('meta[name="csrf-token"]').content;
const $stores = document.querySelector('#tblStores tbody');
const $settingStore = document.getElementById('settingStore');

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

// Event listeners for store management
document.addEventListener('DOMContentLoaded', function() {
    // Store form submission
    document.getElementById('frmStore').addEventListener('submit', saveStore);
    
    // Reset button
    document.getElementById('btnResetForm').addEventListener('click', resetStoreForm);
});

// Make functions globally accessible
window.loadStores = loadStores;
window.fillStore = fillStore;
window.resetStoreForm = resetStoreForm;