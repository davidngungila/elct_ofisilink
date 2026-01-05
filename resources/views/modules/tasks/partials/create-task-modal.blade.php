<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="bx bx-plus-circle"></i> Create New Task
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTaskFormModal">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="action" value="create_task">
                    
                    <div class="form-section-title mb-3">Basics</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Task Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">Select</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="Normal">Normal</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section-title mb-3">Ownership & Dates</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Team Leader *</label>
                            <select name="team_leader_id" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>

                    <div class="form-section-title mb-3">Details</div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Objectives, outcomes, constraints"></textarea>
                    </div>

                    <div class="form-section-title mb-3">Initial Activity (optional)</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Activity Name</label>
                            <input type="text" name="initial_activity_name" class="form-control" placeholder="First activity">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assign To</label>
                            <select name="initial_activity_users[]" class="form-select" multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

