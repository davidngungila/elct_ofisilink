<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
        'category',
        'content',
        'objectives',
        'what_learn',
        'who_teach',
        'location',
        'suggestion_to_saccos',
        'training_timetable',
        'start_date',
        'end_date',
        'max_participants',
        'cost',
        'requires_certificate',
        'send_notifications',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'training_timetable' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TrainingDocument::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(TrainingReport::class)->orderBy('report_date', 'desc');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(TrainingEvaluation::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->evaluations()->avg('overall_rating');
    }

    public function getTotalEvaluationsAttribute()
    {
        return $this->evaluations()->count();
    }

    public function isFull()
    {
        if (!$this->max_participants) {
            return false;
        }
        return $this->participants()->count() >= $this->max_participants;
    }
}

