<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'content',
        'priority',
        'start_date',
        'expiry_date',
        'show_to_all',
        'is_active',
        'require_acknowledgment',
        'allow_redisplay',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'show_to_all' => 'boolean',
        'is_active' => 'boolean',
        'require_acknowledgment' => 'boolean',
        'allow_redisplay' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'notice_role');
    }

    public function acknowledgments(): HasMany
    {
        return $this->hasMany(NoticeAcknowledgment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(NoticeAttachment::class);
    }

    /**
     * Check if notice is currently active
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->expiry_date && $now->gt($this->expiry_date)) {
            return false;
        }

        return true;
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'urgent' => 'bg-danger',
            'important' => 'bg-warning',
            default => 'bg-info',
        };
    }

    /**
     * Check if user has acknowledged this notice
     */
    public function hasAcknowledged(int $userId): bool
    {
        return $this->acknowledgments()->where('user_id', $userId)->exists();
    }

    /**
     * Check if notice should be shown to user
     */
    public function shouldShowToUser(User $user): bool
    {
        if (!$this->isCurrentlyActive()) {
            return false;
        }

        if ($this->show_to_all) {
            return true;
        }

        $userRoles = $user->roles->pluck('id')->toArray();
        $noticeRoles = $this->roles->pluck('id')->toArray();

        return !empty(array_intersect($userRoles, $noticeRoles));
    }
}
