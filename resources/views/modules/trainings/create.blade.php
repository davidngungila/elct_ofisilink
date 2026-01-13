@extends('layouts.app')

@section('title', 'Create Training')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bx bx-plus me-2"></i>Create New Training</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Topic <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('topic') is-invalid @enderror" 
                                       name="topic" value="{{ old('topic') }}" required>
                                @error('topic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                       name="category" value="{{ old('category') }}" 
                                       placeholder="e.g., Technical, Management, Soft Skills">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          name="content" rows="4">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Objectives</label>
                                <textarea class="form-control @error('objectives') is-invalid @enderror" 
                                          name="objectives" rows="3" 
                                          placeholder="List the main objectives of this training">{{ old('objectives') }}</textarea>
                                @error('objectives')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">What Learn</label>
                                <textarea class="form-control @error('what_learn') is-invalid @enderror" 
                                          name="what_learn" rows="3">{{ old('what_learn') }}</textarea>
                                @error('what_learn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Who Teach</label>
                                <input type="text" class="form-control @error('who_teach') is-invalid @enderror" 
                                       name="who_teach" value="{{ old('who_teach') }}">
                                @error('who_teach')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       name="location" value="{{ old('location') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       name="start_date" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Max Participants</label>
                                <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                       name="max_participants" value="{{ old('max_participants') }}" min="1">
                                @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cost (if applicable)</label>
                                <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" 
                                       name="cost" value="{{ old('cost') }}" min="0">
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="requires_certificate" 
                                           value="1" id="requires_certificate" {{ old('requires_certificate') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_certificate">
                                        Requires Certificate
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="send_notifications" 
                                           value="1" id="send_notifications" {{ old('send_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_notifications">
                                        Send Notifications to Participants
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Suggestion to Our Saccos</label>
                                <textarea class="form-control @error('suggestion_to_saccos') is-invalid @enderror" 
                                          name="suggestion_to_saccos" rows="3">{{ old('suggestion_to_saccos') }}</textarea>
                                @error('suggestion_to_saccos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Training Timetable (JSON format or leave empty)</label>
                                <textarea class="form-control @error('training_timetable') is-invalid @enderror" 
                                          name="training_timetable" rows="4" 
                                          placeholder='{"day1": "9:00 AM - 10:00 AM: Introduction", "day2": "..."}'>{{ old('training_timetable') }}</textarea>
                                <small class="text-muted">Enter timetable as JSON object or leave empty</small>
                                @error('training_timetable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Participants</label>
                                <select class="form-select select2 @error('participants') is-invalid @enderror" 
                                        name="participants[]" multiple>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ in_array($user->id, old('participants', [])) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Supportive Documents</label>
                                <input type="file" class="form-control @error('documents.*') is-invalid @enderror" 
                                       name="documents[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <small class="text-muted">You can upload multiple files (Max 10MB each)</small>
                                @error('documents.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('trainings.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Create Training
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select participants',
            allowClear: true
        });
    });
</script>
@endpush

