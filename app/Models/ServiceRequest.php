<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'vehicle_id',
        'requester_id',
        'assigned_to',
        'category',
        'priority',
        'status',
        'subject',
        'description',
        'location',
        'latitude',
        'longitude',
        'attachments',
        'response_time',
        'resolution_time',
        'satisfaction_rating',
        'resolution_notes'
    ];

    protected $casts = [
        'attachments' => 'array',
        'response_time' => 'datetime',
        'resolution_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ticket_number = 'SR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(SupportComment::class, 'commentable');
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'pending' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            'cancelled' => 'dark',
            default => 'secondary'
        };
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeMyRequests($query, $userId)
    {
        return $query->where('requester_id', $userId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }
}