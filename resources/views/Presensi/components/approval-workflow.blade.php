<!--
    Approval Workflow Component
    Displays the approval workflow table for pending attendance approvals
    Usage: @component('Presensi.components.approval-workflow')
-->
@props(['stepNumber' => '4', 'pendingCount' => 0])

<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">{{ $stepNumber }}</span>
                <h5 class="mb-0">Approval Keterlambatan</h5>
                <span class="badge bg-danger ms-2" id="pendingApprovalBadge">{{ $pendingCount }}</span>
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