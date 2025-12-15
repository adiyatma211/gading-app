/**
 * Approval Workflow JavaScript Component
 * Handles attendance approval operations
 */

const csrf = document.querySelector('meta[name="csrf-token"]').content;

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

// Make functions globally accessible
window.loadApprovals = loadApprovals;
window.reviewApproval = reviewApproval;
window.confirmReviewApproval = confirmReviewApproval;