<!--
    Store Management Component
    Displays the store management section with map and form
    Usage: @component('Presensi.components.store-management')
-->
@props(['stepNumber' => '1'])

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">{{ $stepNumber }}</span>
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