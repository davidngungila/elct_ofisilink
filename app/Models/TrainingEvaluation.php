<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'user_id',
        'overall_rating',
        'content_rating',
        'instructor_rating',
        'venue_rating',
        'what_you_liked',
        'what_can_be_improved',
        'additional_comments',
        'would_recommend',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'content_rating' => 'integer',
        'instructor_rating' => 'integer',
        'venue_rating' => 'integer',
        'would_recommend' => 'boolean',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
