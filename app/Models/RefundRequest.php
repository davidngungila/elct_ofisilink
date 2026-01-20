<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'request_no',
        'staff_id',
        'purpose',
        'amount',
        'expense_date',
        'description',
        'status',
        'hod_approved_at',
        'hod_approved_by',
        'hod_comments',
        'accountant_verified_at',
        'accountant_verified_by',
        'accountant_comments',
        'ceo_approved_at',
        'ceo_approved_by',
        'ceo_comments',
        'paid_at',
        'paid_by',
        'payment_method',
        'payment_reference',
        'payment_notes',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'hod_approved_at' => 'datetime',
        'accountant_verified_at' => 'datetime',
        'ceo_approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function attachments()
    {
        return $this->hasMany(RefundAttachment::class);
    }

    public function hodApproval()
    {
        return $this->belongsTo(User::class, 'hod_approved_by');
    }

    public function accountantVerification()
    {
        return $this->belongsTo(User::class, 'accountant_verified_by');
    }

    public function ceoApproval()
    {
        return $this->belongsTo(User::class, 'ceo_approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getProgressPercentageAttribute()
    {
        switch ($this->status) {
            case 'pending_hod':
                return 20;
            case 'pending_accountant':
                return 40;
            case 'pending_ceo':
                return 60;
            case 'approved':
                return 80;
            case 'paid':
                return 100;
            case 'rejected':
                return 0;
            default:
                return 0;
        }
    }

    // Scopes
    public function scopePendingHod($query)
    {
        return $query->where('status', 'pending_hod');
    }

    public function scopePendingAccountant($query)
    {
        return $query->where('status', 'pending_accountant');
    }

    public function scopePendingCeo($query)
    {
        return $query->where('status', 'pending_ceo');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
