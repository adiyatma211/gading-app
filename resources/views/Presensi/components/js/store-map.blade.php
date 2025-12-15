/**
 * Store Map JavaScript Component
 * Handles map functionality for store management
 */

// Leaflet Map variables
let map, marker, circle;

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

// Event listeners for map controls
document.addEventListener('DOMContentLoaded', function() {
    // Radius range and input sync
    document.getElementById('radiusRange').addEventListener('input', (e) => setRadius(e.target.value));
    document.getElementById('frmStore').radius_meters.addEventListener('input', (e) => setRadius(e.target.value));
    
    // Location button
    document.getElementById('btnMyLoc').addEventListener('click', getCurrentLocation);
    
    // Search button
    document.getElementById('btnSearch').addEventListener('click', searchLocation);
});