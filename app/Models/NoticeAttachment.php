<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticeAttachment extends Model
{
    protected $fillable = [
        'notice_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_size',
        'mime_type',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class);
    }
}
