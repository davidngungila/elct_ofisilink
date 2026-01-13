@extends('layouts.app')

@section('title', 'Training Details')

@push('styles')
<style>
    .info-card {
        border-left: 4px solid #667eea;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bx bx-book-open me-2"></i>{{ $training->topic }}</h4>
                    <span class="badge bg-light text-dark">{{ ucfirst($training->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card info-card">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Who Teach</h6>
                                    <p class="mb-0">{{ $training->who_teach ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card info-card">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Location</h6>
                                    <p class="mb-0">{{ $training->location ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @if($training->start_date)
                        <div class="col-md-6 mb-3">
                            <div class="card info-card">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Start Date</h6>
                                    <p class="mb-0">{{ $training->start_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($training->end_date)
                        <div class="col-md-6 mb-3">
                            <div class="card info-card">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">End Date</h6>
                                    <p class="mb-0">{{ $training->end_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($training->content)
                    <div class="mb-4">
                        <h5>Content</h5>
                        <p class="text-muted">{{ $training->content }}</p>
                    </div>
                    @endif

                    @if($training->what_learn)
                    <div class="mb-4">
                        <h5>What Learn</h5>
                        <p class="text-muted">{{ $training->what_learn }}</p>
                    </div>
                    @endif

                    @if($training->suggestion_to_saccos)
                    <div class="mb-4">
                        <h5>Suggestion to Our Saccos</h5>
                        <p class="text-muted">{{ $training->suggestion_to_saccos }}</p>
                    </div>
                    @endif

                    @if($training->training_timetable)
                    <div class="mb-4">
                        <h5>Training Timetable</h5>
                        <div class="card">
                            <div class="card-body">
                                <pre class="mb-0">{{ json_encode($training->training_timetable, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Documents -->
                    <div class="mb-4">
                        <h5>Supportive Documents ({{ $training->documents->count() }})</h5>
                        @if($training->documents->count() > 0)
                            <div class="list-group">
                                @foreach($training->documents as $document)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bx bx-file me-2"></i>
                                        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">
                                            {{ $document->original_name }}
                                        </a>
                                        <small class="text-muted ms-2">({{ number_format($document->file_size / 1024, 2) }} KB)</small>
                                    </div>
                                    @if($canEdit || $document->uploaded_by == auth()->id())
                                    <form action="{{ route('trainings.delete-document', [$training->id, $document->id]) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this document?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No documents uploaded yet.</p>
                        @endif
                    </div>

                    <!-- Participants -->
                    <div class="mb-4">
                        <h5>Participants ({{ $training->participants->count() }})</h5>
                        @if($training->participants->count() > 0)
                            <div class="list-group">
                                @foreach($training->participants as $participant)
                                <div class="list-group-item">
                                    <i class="bx bx-user me-2"></i>
                                    {{ $participant->user->name ?? 'N/A' }}
                                    <span class="badge bg-{{ $participant->status == 'attending' ? 'success' : 'secondary' }} ms-2">
                                        {{ ucfirst($participant->status) }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No participants registered yet.</p>
                        @endif
                    </div>

                    <!-- Reports -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daily Reports ({{ $training->reports->count() }})</h5>
                            @if($canReportTrainings || $isParticipant)
                                <a href="{{ route('trainings.report', $training->id) }}" class="btn btn-sm btn-info">
                                    <i class="bx bx-plus me-1"></i>Submit Daily Report
                                </a>
                            @endif
                        </div>
                        
                        @if($trainingDates && count($trainingDates) > 0)
                            <div class="alert alert-info mb-3">
                                <strong><i class="bx bx-info-circle me-1"></i>Reporting Schedule:</strong>
                                <p class="mb-0 mt-2">You must submit a report for each day of the training period.</p>
                                <div class="mt-2">
                                    <strong>Your Reports:</strong> {{ count($userReports) }} / {{ count($trainingDates) }} days
                                    @if(count($userReports) < count($trainingDates))
                                        <span class="badge bg-warning ms-2">Pending: {{ count($trainingDates) - count($userReports) }} days</span>
                                    @else
                                        <span class="badge bg-success ms-2">All reports submitted</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($training->reports->count() > 0)
                            <div class="list-group">
                                @foreach($training->reports->sortByDesc('report_date') as $report)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <h6 class="mb-0 me-2">{{ $report->report_date->format('M d, Y') }}</h6>
                                                @if(in_array($report->report_date->format('Y-m-d'), $reportedDates ?? []))
                                                    <span class="badge bg-success">Your Report</span>
                                                @endif
                                            </div>
                                            <p class="mb-1">{{ \Illuminate\Support\Str::limit($report->report_content, 100) }}</p>
                                            <small class="text-muted">By: {{ $report->reporter->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bx bx-info-circle me-1"></i>No reports submitted yet. 
                                @if($canReportTrainings || $isParticipant)
                                    <a href="{{ route('trainings.report', $training->id) }}" class="alert-link">Submit your first report</a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Evaluation Section -->
                    @if($isParticipant && $training->status == 'completed')
                    <div class="mb-4">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bx bx-star me-2"></i>Training Evaluation</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $userEvaluation = $training->evaluations->where('user_id', auth()->id())->first();
                                @endphp
                                @if($userEvaluation)
                                    <p class="text-success">
                                        <i class="bx bx-check-circle me-1"></i>You have already submitted your evaluation.
                                        <a href="{{ route('trainings.evaluation', $training->id) }}" class="btn btn-sm btn-outline-info ms-2">
                                            View/Edit Evaluation
                                        </a>
                                    </p>
                                @else
                                    <p>Please take a moment to evaluate this training. Your feedback helps us improve.</p>
                                    <a href="{{ route('trainings.evaluation', $training->id) }}" class="btn btn-info">
                                        <i class="bx bx-star me-1"></i>Submit Evaluation
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('trainings.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to List
                        </a>
                        <div class="d-flex gap-2">
                            @if($canSubmit)
                                <a href="{{ route('trainings.submit', $training->id) }}" class="btn btn-success">
                                    <i class="bx bx-check me-1"></i>Submit Form
                                </a>
                            @endif
                            @if($canReportTrainings || $isParticipant)
                                <a href="{{ route('trainings.report', $training->id) }}" class="btn btn-info">
                                    <i class="bx bx-edit me-1"></i>Daily Report
                                </a>
                            @endif
                            @if($canManageTrainings)
                                <form action="{{ route('trainings.send-notifications', $training->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Send notifications to all participants?');">
                                        <i class="bx bx-bell me-1"></i>Send Notifications
                                    </button>
                                </form>
                            @endif
                            @if($canEdit)
                                <a href="{{ route('trainings.edit', $training->id) }}" class="btn btn-primary">
                                    <i class="bx bx-edit me-1"></i>Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

