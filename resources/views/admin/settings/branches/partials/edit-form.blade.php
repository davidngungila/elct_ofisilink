<div class="mb-3">
  <label class="form-label">Branch Name <span class="text-danger">*</span></label>
  <input type="text" name="name" class="form-control" value="{{ $branch->name }}" required>
</div>
<div class="mb-3">
  <label class="form-label">Branch Code <span class="text-danger">*</span></label>
  <input type="text" name="code" class="form-control" value="{{ $branch->code }}" required placeholder="e.g., HQ, BR001">
  <small class="text-muted">Unique code for this branch</small>
</div>
<div class="mb-3">
  <label class="form-label">Address</label>
  <textarea name="address" class="form-control" rows="2">{{ $branch->address }}</textarea>
</div>
<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control" value="{{ $branch->phone }}">
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ $branch->email }}">
  </div>
</div>
<div class="mb-3">
  <label class="form-label">Managers <span class="text-muted small">(Select multiple)</span></label>
  <select name="manager_ids[]" class="form-select" multiple size="5">
    @foreach($employees as $employee)
      <option value="{{ $employee->id }}" {{ $branch->managers->contains('id', $employee->id) ? 'selected' : '' }}>
        {{ $employee->name }}@if($employee->employee && $employee->employee->employee_id) ({{ $employee->employee->employee_id }})@endif
      </option>
    @endforeach
  </select>
  <small class="text-muted">Hold Ctrl/Cmd to select multiple managers. One manager can manage multiple branches.</small>
</div>
<div class="mb-3">
  <label class="form-label">Notes</label>
  <textarea name="notes" class="form-control" rows="2">{{ $branch->notes }}</textarea>
</div>
<div class="mb-3">
  <div class="form-check">
    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1" {{ $branch->is_active ? 'checked' : '' }}>
    <label class="form-check-label" for="edit_is_active">
      Active
    </label>
  </div>
</div>
