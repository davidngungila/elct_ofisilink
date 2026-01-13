@extends('layouts.app')

@section('title', 'Training Daily Report')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="bx bx-edit me-2"></i>Submit Daily Report</h4>
                </div>
                <div class="card-body">
                    @if(isset($permissionRequest) && $permissionRequest)
                        <div class="alert alert-success mb-3">
                            <i class="bx bx-check-circle me-2"></i>
                            <strong>Permission-Based Reporting:</strong> You have an approved permission request for training. 
                            You must report for each day requested in your permission.
                            <br><small>Permission: {{ $permissionRequest->request_id }} | 
                            Dates: {{ $permissionRequest->start_datetime->format('M d, Y') }} - {{ $permissionRequest->end_datetime->format('M d, Y') }}</small>
                        </div>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Daily Reporting Required:</strong> You must submit a report for each day of the training. 
                            If you've already submitted a report for a date, submitting again will update that report.
                        </div>
                    @endif
                    
                    <form action="{{ route('trainings.store-report', $training->id) }}" method="POST">
                        @csrf
                        
                        @if(isset($permissionRequest) && $permissionRequest)
                            <input type="hidden" name="permission_request_id" value="{{ $permissionRequest->id }}">
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label">Report Date <span class="text-danger">*</span></label>
                            @if(isset($permissionDates) && count($permissionDates) > 0)
                                <select class="form-select @error('report_date') is-invalid @enderror" 
                                       name="report_date" required>
                                    <option value="">-- Select Date --</option>
                                    @foreach($permissionDates as $date)
                                        @php
                                            $dateObj = \Carbon\Carbon::parse($date);
                                            $isReported = in_array($date, $reportedDates ?? []);
                                        @endphp
                                        <option value="{{ $date }}" {{ old('report_date') == $date ? 'selected' : '' }}>
                                            {{ $dateObj->format('M d, Y (l)') }}
                                            @if($isReported) - [Already Reported] @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    <i class="bx bx-info-circle"></i> Select a date from your approved permission dates.
                                </small>
                            @else
                                <input type="date" class="form-control @error('report_date') is-invalid @enderror" 
                                       name="report_date" value="{{ old('report_date', date('Y-m-d')) }}" required
                                       @if($training->start_date) min="{{ $training->start_date->format('Y-m-d') }}" @endif
                                       @if($training->end_date) max="{{ $training->end_date->format('Y-m-d') }}" @endif>
                            @endif
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($permissionDates) && count($permissionDates) > 0)
                                <div class="mt-2">
                                    <small class="text-info">
                                        <strong>Remaining dates to report:</strong> 
                                        {{ count($permissionDates) - count(array_intersect($permissionDates, $reportedDates ?? [])) }} / {{ count($permissionDates) }}
                                    </small>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Report Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('report_content') is-invalid @enderror" 
                                      name="report_content" rows="6" required>{{ old('report_content') }}</textarea>
                            @error('report_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Activities Completed</label>
                            <textarea class="form-control @error('activities_completed') is-invalid @enderror" 
                                      name="activities_completed" rows="4">{{ old('activities_completed') }}</textarea>
                            @error('activities_completed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Challenges Faced</label>
                            <textarea class="form-control @error('challenges_faced') is-invalid @enderror" 
                                      name="challenges_faced" rows="4">{{ old('challenges_faced') }}</textarea>
                            @error('challenges_faced')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Next Day Plan</label>
                            <textarea class="form-control @error('next_day_plan') is-invalid @enderror" 
                                      name="next_day_plan" rows="4">{{ old('next_day_plan') }}</textarea>
                            @error('next_day_plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('trainings.show', $training->id) }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="bx bx-save me-1"></i>Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bx bx-clipboard me-2"></i>Previous Reports</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Your Reports:</strong> {{ count($userReports ?? []) }} submitted
                    </div>
                    @if(isset($userReports) && $userReports->count() > 0)
                        <div class="list-group">
                            @foreach($userReports as $report)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $report->report_date->format('M d, Y') }}</h6>
                                        <p class="mb-1 small">{{ \Illuminate\Support\Str::limit($report->report_content, 80) }}</p>
                                    </div>
                                    <span class="badge bg-success">Submitted</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No reports submitted yet</p>
                    @endif
                    
                    @if(isset($userReports) && $reports->count() > $userReports->count())
                        <hr>
                        <div class="mt-3">
                            <strong>All Reports ({{ $reports->count() }}):</strong>
                            <div class="list-group mt-2">
                                @foreach($reports->take(5) as $report)
                                <div class="list-group-item">
                                    <h6 class="mb-1">{{ $report->report_date->format('M d, Y') }}</h6>
                                    <p class="mb-1 small">{{ \Illuminate\Support\Str::limit($report->report_content, 80) }}</p>
                                    <small class="text-muted">By: {{ $report->reporter->name ?? 'N/A' }}</small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

