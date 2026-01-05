<script>
$(document).ready(function() {
    const actionUrl = '{{ route("modules.tasks.action") }}';
    const csrfToken = '{{ csrf_token() }}';
    
    // Create Task Form (Modal)
    $('#createTaskFormModal').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message || 'Task created successfully', 'success');
                    $('#createTaskModal').modal('hide');
                    $('#createTaskFormModal')[0].reset();
                    location.reload();
                } else {
                    Swal.fire('Error!', response.message || 'Failed to create task', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response?.message || 'Failed to create task', 'error');
            }
        });
    });
    
    // Submit Progress Form (Modal)
    $('#submitProgressFormModal').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.set('action', 'task_submit_report'); // Update action name
        
        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message || 'Progress submitted successfully', 'success');
                    $('#submitProgressModal').modal('hide');
                    $('#submitProgressFormModal')[0].reset();
                    $('#modalAttachmentPreview').empty();
                    location.reload();
                } else {
                    Swal.fire('Error!', response.message || 'Failed to submit progress', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response?.message || 'Failed to submit progress', 'error');
            }
        });
    });
    
    // Preview selected files for dashboard form
    $('#reportAttachments').on('change', function() {
        const files = this.files;
        const preview = $('#attachmentPreview');
        preview.empty();
        
        if (files.length > 0) {
            preview.append('<small class="text-muted d-block mb-1">Selected files:</small><ul class="list-unstyled mb-0">');
            Array.from(files).forEach(function(file) {
                const size = (file.size / 1024).toFixed(2);
                preview.append(`<li><i class="bx bx-file"></i> ${file.name} <small class="text-muted">(${size} KB)</small></li>`);
            });
            preview.append('</ul>');
        }
    });
    
    // Create Task Form (Dashboard)
    $('#createTaskForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'create_task');
        
        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message || 'Task created successfully', 'success');
                    $('#createTaskForm')[0].reset();
                    location.reload();
                } else {
                    Swal.fire('Error!', response.message || 'Failed to create task', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response?.message || 'Failed to create task', 'error');
            }
        });
    });
    
    // Progress Form (Dashboard)
    $('#progressForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'task_submit_report');
        
        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message || 'Progress submitted successfully', 'success');
                    $('#progressForm')[0].reset();
                    location.reload();
                } else {
                    Swal.fire('Error!', response.message || 'Failed to submit progress', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response?.message || 'Failed to submit progress', 'error');
            }
        });
    });
    
    // Completion status change handler
    $('#completionStatus, #modalCompletionStatus').on('change', function() {
        const status = $(this).val();
        const wrap = $(this).attr('id') === 'completionStatus' ? $('#delayReasonWrap') : $('#modalDelayReasonWrap');
        if (status === 'Behind Schedule' || status === 'Delayed') {
            wrap.show();
        } else {
            wrap.hide();
        }
    });
    
    // List View Filters
    $('#taskSearchInput').on('keyup', function() {
        const search = $(this).val().toLowerCase();
        $('#tasksListTable tbody tr').each(function() {
            const taskName = $(this).data('task-name') || '';
            $(this).toggle(taskName.includes(search));
        });
    });
    
    $('#filterStatus, #filterPriority').on('change', function() {
        const status = $('#filterStatus').val();
        const priority = $('#filterPriority').val();
        
        $('#tasksListTable tbody tr').each(function() {
            const rowStatus = $(this).data('status');
            const rowPriority = $(this).data('priority');
            let show = true;
            
            if (status && rowStatus !== status) show = false;
            if (priority && rowPriority !== priority) show = false;
            
            $(this).toggle(show);
        });
    });
    
    // Reports Filter
    $('#filterReportStatus').on('change', function() {
        const status = $(this).val();
        $('#reportsTable tbody tr').each(function() {
            if (!status) {
                $(this).show();
            } else {
                const rowStatus = $(this).data('status');
                $(this).toggle(rowStatus === status);
            }
        });
    });
    
    // Activity Search
    $('#activitySearch').on('keyup', function() {
        const search = $(this).val().toLowerCase();
        $('#activitiesTable tbody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(search));
        });
    });
    
    // Global Functions
    window.openReportForm = function(activityId) {
        if (activityId && activityId !== 'null') {
            $('#modalProgressActivitySelect').val(activityId);
        }
        $('#submitProgressModal').modal('show');
    };
    
    window.viewTaskDetails = function(taskId) {
        $('#viewTaskModal').modal('show');
        $('#view-task-content').html(`
            <div class="modal-header">
                <h5 class="modal-title">Loading Task...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Loading task details...</p>
            </div>
        `);
        
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('action', 'get_task_full_details');
        formData.append('task_id', taskId);
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.task) {
                const task = data.task;
                const totalActivities = task.activities?.length || 0;
                const completedActivities = task.activities?.filter(a => a.status === 'Completed').length || 0;
                const progress = totalActivities > 0 ? Math.round((completedActivities / totalActivities) * 100) : 0;
                
                $('#view-task-content').html(`
                    <div class="modal-header">
                        <h5 class="modal-title">${escapeHtml(task.name)}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Status:</strong> <span class="badge bg-primary">${task.status || 'N/A'}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Priority:</strong> <span class="badge bg-warning">${task.priority || 'Normal'}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Team Leader:</strong> ${task.team_leader?.name || 'Unassigned'}
                            </div>
                            <div class="col-md-6">
                                <strong>Progress:</strong> ${progress}% (${completedActivities}/${totalActivities} activities)
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p>${escapeHtml(task.description || 'No description provided')}</p>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Start Date:</strong> ${task.start_date || 'N/A'}
                            </div>
                            <div class="col-md-6">
                                <strong>End Date:</strong> ${task.end_date || 'N/A'}
                            </div>
                        </div>
                        ${totalActivities > 0 ? `
                            <div class="mb-3">
                                <strong>Activities (${totalActivities}):</strong>
                                <ul class="list-group mt-2">
                                    ${task.activities.map(activity => `
                                        <li class="list-group-item">
                                            <strong>${escapeHtml(activity.name)}</strong> - 
                                            <span class="badge bg-secondary">${activity.status || 'Not Started'}</span>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="/modules/tasks/pdf?task_id=${taskId}" class="btn btn-primary" target="_blank">Download PDF</a>
                    </div>
                `);
            } else {
                $('#view-task-content').html(`
                    <div class="modal-header">
                        <h5 class="modal-title">Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">${data.message || 'Failed to load task details'}</div>
                    </div>
                `);
            }
        })
        .catch(error => {
            console.error('Error loading task details:', error);
            $('#view-task-content').html(`
                <div class="modal-header">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">Network error. Please try again.</div>
                </div>
            `);
        });
    };
    
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }
    
    window.editTask = function(taskId) {
        $('#editTaskModal').modal('show');
        $('#edit-task-content').html(`
            <div class="modal-header">
                <h5 class="modal-title">Loading Task...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Loading task details...</p>
            </div>
        `);
        
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('action', 'get_task_full_details');
        formData.append('task_id', taskId);
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.task) {
                const task = data.task;
                
                // Build users select options - use passed users or empty array
                const usersOptions = @json($users ?? []);
                const usersSelectOptions = usersOptions.map(user => 
                    `<option value="${user.id}" ${task.team_leader_id == user.id ? 'selected' : ''}>${escapeHtml(user.name)}</option>`
                ).join('');
                
                // Build categories select options - use passed categories or empty array
                const categoriesOptions = @json($categories ?? []);
                const categoriesSelectOptions = categoriesOptions.map(cat => 
                    `<option value="${escapeHtml(cat.name)}" ${task.category === cat.name ? 'selected' : ''}>${escapeHtml(cat.name)}</option>`
                ).join('');
                
                // Build tags string
                const tagsString = Array.isArray(task.tags) ? task.tags.join(', ') : (task.tags || '');
                
                $('#edit-task-content').html(`
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white">
                            <i class="bx bx-edit"></i> Edit Task
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editTaskForm">
                        <div class="modal-body">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="action" value="task_edit_main">
                            <input type="hidden" name="main_task_id" value="${task.id}">
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Task Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="${escapeHtml(task.name)}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="3" class="form-control">${escapeHtml(task.description || '')}</textarea>
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="">Select Category</option>
                                        ${categoriesSelectOptions}
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select">
                                        <option value="Normal" ${task.priority === 'Normal' ? 'selected' : ''}>Normal</option>
                                        <option value="High" ${task.priority === 'High' ? 'selected' : ''}>High</option>
                                        <option value="Critical" ${task.priority === 'Critical' ? 'selected' : ''}>Critical</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Team Leader <span class="text-danger">*</span></label>
                                    <select name="team_leader_id" class="form-select" required>
                                        <option value="">Select Team Leader</option>
                                        ${usersSelectOptions}
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="${task.start_date || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="${task.end_date || ''}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Timeframe</label>
                                    <input type="text" name="timeframe" class="form-control" value="${escapeHtml(task.timeframe || '')}" placeholder="e.g., 3 Weeks">
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" ${task.status === 'pending' ? 'selected' : ''}>Pending</option>
                                        <option value="in_progress" ${task.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                        <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Completed</option>
                                        <option value="on_hold" ${task.status === 'on_hold' ? 'selected' : ''}>On Hold</option>
                                        <option value="cancelled" ${task.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Budget</label>
                                    <input type="text" name="budget" class="form-control" value="${escapeHtml(task.budget || '')}" placeholder="Enter budget if applicable">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tags</label>
                                <input type="text" name="tags" class="form-control" value="${escapeHtml(tagsString)}" placeholder="Comma-separated tags">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Task</button>
                        </div>
                    </form>
                `);
                
                // Handle form submission
                $('#editTaskForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    
                    const editFormData = new FormData(this);
                    
                    Swal.fire({
                        title: 'Updating Task...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    fetch(actionUrl, {
                        method: 'POST',
                        body: editFormData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message || 'Task updated successfully',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#editTaskModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Failed to update task'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating task:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the task'
                        });
                    });
                });
            } else {
                $('#edit-task-content').html(`
                    <div class="modal-header">
                        <h5 class="modal-title">Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">${data.message || 'Failed to load task details'}</div>
                    </div>
                `);
            }
        })
        .catch(error => {
            console.error('Error loading task details:', error);
            $('#edit-task-content').html(`
                <div class="modal-header">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">Network error. Please try again.</div>
                </div>
            `);
        });
    };
    
    window.approveReport = function(reportId) {
        Swal.fire({
            title: 'Approve Report?',
            text: 'Are you sure you want to approve this progress report?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('action', 'task_approve_report');
                formData.append('report_id', reportId);
                
                fetch(actionUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Approved!', data.message || 'Report approved successfully', 'success');
                        location.reload();
                    } else {
                        Swal.fire('Error!', data.message || 'Failed to approve report', 'error');
                    }
                });
            }
        });
    };
    
    window.rejectReport = function(reportId) {
        Swal.fire({
            title: 'Reject Report?',
            input: 'textarea',
            inputPlaceholder: 'Enter reason for rejection...',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Reject',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please provide a reason for rejection';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('action', 'task_reject_report');
                formData.append('report_id', reportId);
                formData.append('comments', result.value);
                
                fetch(actionUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Rejected!', data.message || 'Report rejected', 'success');
                        location.reload();
                    } else {
                        Swal.fire('Error!', data.message || 'Failed to reject report', 'error');
                    }
                });
            }
        });
    };
});
</script>

