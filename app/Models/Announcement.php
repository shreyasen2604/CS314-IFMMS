<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'created_by',
        'type',
        'target_audience',
        'is_active',
        'publish_at',
        'expire_at',
        'attachments',
        'views_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_at' => 'datetime',
        'expire_at' => 'datetime',
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who created the announcement
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('publish_at')
                          ->orWhere('publish_at', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('expire_at')
                          ->orWhere('expire_at', '>', now());
                    });
    }

    /**
     * Scope for announcements by target audience
     */
    public function scopeForAudience($query, $role)
    {
        // Convert role to match target_audience format
        $audienceRole = strtolower($role);
        
        // Map singular roles to plural for target_audience
        $roleMap = [
            'admin' => 'admins',
            'driver' => 'drivers',
            'technician' => 'technicians'
        ];
        
        $targetRole = $roleMap[$audienceRole] ?? $audienceRole;
        
        return $query->where(function($q) use ($targetRole) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', $targetRole);
        });
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Check if announcement is currently active
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->publish_at && $this->publish_at->isFuture()) {
            return false;
        }

        if ($this->expire_at && $this->expire_at->isPast()) {
            return false;
        }

        return true;
    }
}