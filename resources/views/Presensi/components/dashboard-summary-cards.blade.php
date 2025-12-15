<!--
    Dashboard Summary Cards Component
    Displays summary cards for key metrics
    Usage: @component('Presensi.components.dashboard-summary-cards')
-->
@props(['totalStores' => 0, 'activeSettings' => 0, 'totalHolidays' => 0, 'pendingApprovals' => 0])

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
                        <h4 class="mb-0" id="totalStores">{{ $totalStores }}</h4>
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
                        <h4 class="mb-0" id="activeSettings">{{ $activeSettings }}</h4>
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
                        <h4 class="mb-0" id="totalHolidays">{{ $totalHolidays }}</h4>
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
                        <h4 class="mb-0" id="pendingApprovals">{{ $pendingApprovals }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>