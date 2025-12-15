/**
 * Holiday Management JavaScript Component
 * Handles holiday CRUD operations
 */

const csrf = document.querySelector('meta[name="csrf-token"]').content;

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

// Event listeners for holiday management
document.addEventListener('DOMContentLoaded', function() {
    // Add holiday button
    document.getElementById('btnAddHoliday').addEventListener('click', addHoliday);
});

// Make functions globally accessible
window.loadHolidays = loadHolidays;
window.delHoliday = delHoliday;
window.confirmDelHoliday = confirmDelHoliday;