@extends('layouts.app')

@section('title', 'Task Categories - OfisiLink')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title text-white mb-1">
                                <i class="bx bx-category me-2"></i>Task Categories
                            </h4>
                            <p class="card-text text-white-50 mb-0">Manage task categories and organization</p>
                        </div>
                        <div>
                            <a href="{{ route('modules.tasks.index') }}" class="btn btn-light">
                                <i class="bx bx-arrow-back me-1"></i>Back to Tasks
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><i class="bx bx-list-ul me-2"></i>All Categories</h5>
                        <small class="text-muted">Showing all {{ $categories->count() }} categories</small>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bx bx-plus me-1"></i>Add Category
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-primary">Total Categories: {{ $categories->count() }}</span>
                        <span class="badge bg-success ms-2">Active: {{ $categories->where('is_active', true)->count() }}</span>
                        <span class="badge bg-secondary ms-2">Inactive: {{ $categories->where('is_active', false)->count() }}</span>
                    </div>
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Tasks Count</th>
                                        <th>Sort Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                    <tr>
                                        <td><strong>{{ $category->name }}</strong></td>
                                        <td>{{ $category->description ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->tasks_count ?? 0 }}</span>
                                        </td>
                                        <td>{{ $category->sort_order ?? 0 }}</td>
                                        <td>
                                            <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editCategory({{ $category->id }})" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCategory({{ $category->id }})" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-category fs-1 text-muted mb-3"></i>
                            <p class="text-muted">No categories found. Create your first category.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm" action="{{ route('modules.tasks.action') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="create_category">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" action="{{ route('modules.tasks.action') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="update_category">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" id="edit_category_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_category_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="edit_category_sort_order" class="form-control">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
function editCategory(id) {
    // Load category data via AJAX and populate edit modal
    $.ajax({
        url: '{{ route("modules.tasks.action") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            action: 'get_category',
            category_id: id
        },
        success: function(response) {
            if (response.success) {
                $('#edit_category_id').val(response.category.id);
                $('#edit_category_name').val(response.category.name);
                $('#edit_category_description').val(response.category.description);
                $('#edit_category_sort_order').val(response.category.sort_order);
                $('#edit_is_active').prop('checked', response.category.is_active);
                $('#editCategoryModal').modal('show');
            }
        }
    });
}

function deleteCategory(id) {
    Swal.fire({
        title: 'Delete Category?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("modules.tasks.action") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: 'delete_category',
                    category_id: id
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'Category deleted successfully', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to delete category', 'error');
                    }
                }
            });
        }
    });
}

$('#addCategoryForm, #editCategoryForm').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', 'Category saved successfully', 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', response.message || 'Failed to save category', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to save category. Please try again.', 'error');
        }
    });
});
</script>
@endpush

