<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for this branch
     */
    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    /**
     * Get the managers for this branch (many-to-many)
     */
    public function managers()
    {
        return $this->belongsToMany(User::class, 'branch_managers', 'branch_id', 'user_id')
                    ->withTimestamps();
    }
}
