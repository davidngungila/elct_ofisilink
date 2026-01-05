<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityReportAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(ActivityReport::class, 'report_id');
    }

    /**
     * Get the full URL for the file
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Check if file is an image
     */
    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }
}
