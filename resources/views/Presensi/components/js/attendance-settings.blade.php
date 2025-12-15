/**
 * Attendance Settings JavaScript Component
 * Handles attendance settings configuration
 */

const csrf = document.querySelector('meta[name="csrf-token"]').content;
const $settingStore = document.getElementById('settingStore');

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

// Event listeners for attendance settings
document.addEventListener('DOMContentLoaded', function() {
    // Store selection change
    $settingStore.addEventListener('change', loadSetting);
    
    // Save settings button
    document.getElementById('btnSaveSetting').addEventListener('click', saveAttendanceSettings);
});

// Make functions globally accessible
window.loadSetting = loadSetting;
window.saveAttendanceSettings = saveAttendanceSettings;