@extends('layouts.app')

@section('title', 'Branch Management - OfisiLink')

@section('breadcrumb')
<div class="row">
  <div class="col-12">
    <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden; background: linear-gradient(135deg, #940000 0%, #a80000 50%, #940000 100%); background-size: 400% 400%; animation: gradientShift 15s ease infinite;">
      <div class="card-body text-white p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="mb-3 mb-md-0">
            <h3 class="mb-2 text-white fw-bold">
              <i class="bx bx-map me-2"></i>Branch Management
            </h3>
            <p class="mb-0 text-white-50 fs-6">
              Manage branches and assign users to branches
            </p>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.settings') }}" class="btn btn-light btn-lg shadow-sm">
              <i class="bx bx-arrow-back me-2"></i>Back to Settings
            </a>
            <button type="button" class="btn btn-light btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#createBranchModal">
              <i class="bx bx-plus me-2"></i>Add Branch
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<br>
@endsection

@section('content')

<!-- Branches List -->
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="bx bx-map me-2"></i>All Branches
        </h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Manager</th>
                <th>Users</th>
                <th>Status</th>
                <th width="150">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($branches as $branch)
              <tr>
                <td>
                  <strong>{{ $branch->name }}</strong>
                </td>
                <td>
                  <span class="badge bg-label-primary">{{ $branch->code }}</span>
                </td>
                <td>{{ $branch->address ?? 'N/A' }}</td>
                <td>{{ $branch->phone ?? 'N/A' }}</td>
                <td>{{ $branch->email ?? 'N/A' }}</td>
                <td>
                  @if($branch->managers && $branch->managers->count() > 0)
                    @foreach($branch->managers as $manager)
                      <span class="badge bg-label-primary me-1 mb-1">{{ $manager->name }}</span>
                    @endforeach
                  @else
                    <span class="text-muted">No managers assigned</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-label-info">{{ $branch->users()->count() }}</span>
                </td>
                <td>
                  <span class="badge bg-{{ $branch->is_active ? 'success' : 'danger' }}">
                    {{ $branch->is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-sm btn-outline-info" title="View Details">
                      <i class="bx bx-show"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editBranch({{ $branch->id }})" title="Edit">
                      <i class="bx bx-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteBranch({{ $branch->id }})" title="Delete">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center py-4">
                  <div class="text-muted">
                    <i class="bx bx-info-circle fs-4 d-block mb-2"></i>
                    No branches found. Create your first branch to get started.
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Create Branch Modal -->
<div class="modal fade" id="createBranchModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.branches.store') }}" method="POST" id="createBranchForm">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title text-white">
            <i class="bx bx-plus me-2"></i>Create New Branch
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Branch Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Branch Code <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control" required placeholder="e.g., HQ, BR001">
            <small class="text-muted">Unique code for this branch</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Managers <span class="text-muted small">(Select multiple)</span></label>
            <select name="manager_ids[]" class="form-select" multiple size="5">
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}@if($employee->employee && $employee->employee->employee_id) ({{ $employee->employee->employee_id }})@endif</option>
              @endforeach
            </select>
            <small class="text-muted">Hold Ctrl/Cmd to select multiple managers. One manager can manage multiple branches.</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
              <label class="form-check-label" for="is_active">
                Active
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-2"></i>Create Branch
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Branch Modal -->
<div class="modal fade" id="editBranchModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editBranchForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title text-white">
            <i class="bx bx-edit me-2"></i>Edit Branch
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="editBranchBody">
          <!-- Content will be loaded via AJAX -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-2"></i>Update Branch
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
function editBranch(branchId) {
  fetch(`{{ url('admin/branches') }}/${branchId}/edit`, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'text/html'
    }
  })
    .then(response => response.text())
    .then(html => {
      document.getElementById('editBranchBody').innerHTML = html;
      document.getElementById('editBranchForm').action = `{{ url('admin/branches') }}/${branchId}`;
      new bootstrap.Modal(document.getElementById('editBranchModal')).show();
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire('Error', 'Error loading branch data', 'error');
    });
}

function deleteBranch(branchId) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the branch. This action cannot be undone!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `{{ url('admin/branches') }}/${branchId}`;
      
      const csrf = document.createElement('input');
      csrf.type = 'hidden';
      csrf.name = '_token';
      csrf.value = '{{ csrf_token() }}';
      form.appendChild(csrf);
      
      const method = document.createElement('input');
      method.type = 'hidden';
      method.name = '_method';
      method.value = 'DELETE';
      form.appendChild(method);
      
      document.body.appendChild(form);
      form.submit();
    }
  });
}

// Handle form submissions with success messages
document.addEventListener('DOMContentLoaded', function() {
  const createForm = document.getElementById('createBranchForm');
  if (createForm) {
    createForm.addEventListener('submit', function(e) {
      // Form will submit normally, success message handled by Laravel flash
    });
  }
  
  const editForm = document.getElementById('editBranchForm');
  if (editForm) {
    editForm.addEventListener('submit', function(e) {
      // Form will submit normally, success message handled by Laravel flash
    });
  }
  
  // Show flash messages
  @if(session('success'))
    Swal.fire('Success', '{{ session('success') }}', 'success');
  @endif
  
  @if(session('error'))
    Swal.fire('Error', '{{ session('error') }}', 'error');
  @endif
});
</script>
@endpush
