<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollFormula extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_type',
        'name',
        'formula',
        'explanation',
        'parameters',
        'is_locked',
        'locked_by',
        'locked_at',
        'otp_code',
        'otp_expires_at',
        'is_active',
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_locked' => 'boolean',
        'is_active' => 'boolean',
        'locked_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    /**
     * Get the user who locked this formula
     */
    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Check if formula is currently locked
     */
    public function isCurrentlyLocked()
    {
        return $this->is_locked && $this->locked_at;
    }

    /**
     * Check if OTP is valid
     */
    public function isOtpValid($otp)
    {
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }

        if (now()->greaterThan($this->otp_expires_at)) {
            return false;
        }

        return $this->otp_code === $otp;
    }
}
