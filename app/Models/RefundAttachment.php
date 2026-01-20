<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundAttachment extends Model
{
    protected $fillable = [
        'refund_request_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'uploaded_by'
    ];

    // Relationships
    public function refundRequest()
    {
        return $this->belongsTo(RefundRequest::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
