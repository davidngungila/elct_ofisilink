@extends('layouts.app')

@section('title', 'Training Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    .training-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }
    .training-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg bg-primary" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-3 mb-md-0">
                            <h3 class="mb-2 text-white fw-bold">
                                <i class="bx bx-book-open me-2"></i>Training Management
                            </h3>
                            <p class="mb-0 text-white-50 fs-6">
                                Manage trainings, submit forms, and track daily reports
                            </p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            @if($canManageTrainings)
                                <a href="{{ route('trainings.analytics') }}" class="btn btn-light btn-lg shadow-sm">
                                    <i class="bx bx-bar-chart me-2"></i>Analytics
                                </a>
                                <a href="{{ route('trainings.calendar') }}" class="btn btn-light btn-lg shadow-sm">
                                    <i class="bx bx-calendar me-2"></i>Calendar
                                </a>
                                <a href="{{ route('trainings.export-pdf') }}" class="btn btn-light btn-lg shadow-sm">
                                    <i class="bx bx-download me-2"></i>Export PDF
                                </a>
                                <a href="{{ route('trainings.create') }}" class="btn btn-light btn-lg shadow-sm">
                                    <i class="bx bx-plus me-2"></i>Create Training
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('trainings.search') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search trainings..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                @if(isset($categories))
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="start_date" 
                                   placeholder="Start Date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-search me-1"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Trainings List -->
    <div class="row">
        @forelse($trainings as $training)
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card training-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $training->topic }}</h5>
                        @if($training->category)
                            <small class="text-muted">{{ $training->category }}</small>
                        @endif
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        @if($training->isFull())
                            <span class="badge bg-warning">Full</span>
                        @endif
                        <span class="badge bg-{{ $training->status == 'published' ? 'success' : ($training->status == 'ongoing' ? 'info' : 'secondary') }}">
                            {{ ucfirst($training->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">
                        <i class="bx bx-user me-1"></i>Instructor: {{ $training->who_teach ?? 'N/A' }}
                    </p>
                    <p class="text-muted mb-2">
                        <i class="bx bx-map me-1"></i>Location: {{ $training->location ?? 'N/A' }}
                    </p>
                    @if($training->start_date)
                    <p class="text-muted mb-2">
                        <i class="bx bx-calendar me-1"></i>
                        {{ $training->start_date->format('M d, Y') }}
                        @if($training->end_date)
                            - {{ $training->end_date->format('M d, Y') }}
                        @endif
                    </p>
                    @endif
                    <p class="text-muted mb-3">
                        <i class="bx bx-file me-1"></i>{{ $training->documents->count() }} Documents
                        <span class="ms-3"><i class="bx bx-clipboard me-1"></i>{{ $training->reports->count() }} Reports</span>
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('trainings.show', $training->id) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-show me-1"></i>View Details
                        </a>
                        @if($canReportTrainings || $training->participants->contains('user_id', auth()->id()))
                            <a href="{{ route('trainings.report', $training->id) }}" class="btn btn-sm btn-info">
                                <i class="bx bx-edit me-1"></i>Report
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-book-open fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No trainings found</h5>
                    @if($canManageTrainings)
                        <a href="{{ route('trainings.create') }}" class="btn btn-primary mt-3">
                            <i class="bx bx-plus me-1"></i>Create First Training
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($trainings->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            {{ $trainings->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

