<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'branch_id',
        'name',
        'time_mode',
        'start_datetime',
        'end_datetime',
        'reason_type',
        'reason_description',
        'training_id',
        'is_for_training',
        'status',
        'hr_initial_reviewed',
        'hr_initial_reviewed_by',
        'hr_initial_comments',
        'hod_reviewed',
        'hod_reviewed_by',
        'hod_comments',
        'hr_final_reviewed',
        'hr_final_reviewed_by',
        'hr_final_comments',
        'return_datetime',
        'return_remarks',
        'return_submitted_at',
        'hod_return_reviewed',
        'hod_return_comments',
        'branch_manager_approved',
        'branch_manager_approved_by',
        'branch_manager_approved_at',
        'branch_manager_comments',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'return_datetime' => 'datetime',
        'hr_initial_reviewed' => 'datetime',
        'hod_reviewed' => 'datetime',
        'hr_final_reviewed' => 'datetime',
        'return_submitted_at' => 'datetime',
        'hod_return_reviewed' => 'datetime',
        'branch_manager_approved' => 'boolean',
        'branch_manager_approved_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function branchManager()
    {
        return $this->belongsTo(User::class, 'branch_manager_approved_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hrInitialReviewer()
    {
        return $this->belongsTo(User::class, 'hr_initial_reviewed_by');
    }

    public function hodReviewer()
    {
        return $this->belongsTo(User::class, 'hod_reviewed_by');
    }

    public function hrFinalReviewer()
    {
        return $this->belongsTo(User::class, 'hr_final_reviewed_by');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending_hr' => ['class' => 'warning', 'text' => 'Pending HR Review'],
            'pending_hod' => ['class' => 'info', 'text' => 'Pending HOD Approval'],
            'pending_hr_final' => ['class' => 'primary', 'text' => 'Pending HR Final Approval'],
            'approved' => ['class' => 'success', 'text' => 'Approved'],
            'rejected' => ['class' => 'danger', 'text' => 'Rejected'],
            'in_progress' => ['class' => 'info', 'text' => 'In Progress'],
            'return_pending' => ['class' => 'warning', 'text' => 'Return Pending HR Verification'],
            'return_rejected' => ['class' => 'danger', 'text' => 'Return Rejected'],
            'completed' => ['class' => 'secondary', 'text' => 'Completed'],
        ];

        return $badges[$this->status] ?? ['class' => 'secondary', 'text' => ucwords(str_replace('_', ' ', $this->status))];
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function trainingReports()
    {
        return $this->hasMany(TrainingReport::class);
    }

    /**
     * Get all dates between start and end datetime
     */
    public function getRequestedDatesAttribute()
    {
        if (!$this->start_datetime || !$this->end_datetime) {
            return [];
        }

        $dates = [];
        $current = $this->start_datetime->copy()->startOfDay();
        $end = $this->end_datetime->copy()->startOfDay();

        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Check if permission is for training
     */
    public function isForTraining()
    {
        return $this->is_for_training || 
               $this->training_id !== null ||
               (stripos($this->reason_description ?? '', 'training') !== false);
    }
}
