<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'permission_request_id',
        'report_date',
        'report_content',
        'activities_completed',
        'challenges_faced',
        'next_day_plan',
        'created_by',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function permissionRequest(): BelongsTo
    {
        return $this->belongsTo(PermissionRequest::class);
    }
}


