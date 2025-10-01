<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Class Incident
 * 
 * Represents a vehicle incident/issue report in the IFMMS system.
 * Tracks vehicle problems, assignments, and resolution status.
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $category
 * @property string $severity
 * @property string $status
 * @property int|null $reported_by_user_id
 * @property int|null $assigned_to_user_id
 * @property string|null $vehicle_identifier
 * @property int|null $odometer
 * @property float|null $latitude
 * @property float|null $longitude
 * @property array|null $dtc_codes
 * @property string|null $fuel_level
 * @property string|null $location_description
 * @property string|null $weather_conditions
 * @property string|null $road_conditions
 * @property string|null $additional_notes
 * @property Carbon|null $acknowledged_at
 * @property Carbon|null $dispatched_at
 * @property Carbon|null $started_at
 * @property Carbon|null $resolved_at
 * @property Carbon|null $closed_at
 * @property Carbon|null $sla_response_due_at
 * @property Carbon|null $sla_resolution_due_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Incident extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'incidents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        // Basic Information
        'title',
        'description',
        'category',
        'severity',
        'status',
        
        // User Assignments
        'reported_by_user_id',
        'assigned_to_user_id',
        
        // Vehicle Information
        'vehicle_identifier',
        'odometer',
        'fuel_level',
        'dtc_codes',
        
        // Location Information
        'latitude',
        'longitude',
        'location_description',
        'weather_conditions',
        'road_conditions',
        
        // Additional Details
        'additional_notes',
        
        // Timeline Tracking
        'acknowledged_at',
        'dispatched_at',
        'started_at',
        'resolved_at',
        'closed_at',
        
        // SLA Tracking
        'sla_response_due_at',
        'sla_resolution_due_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'acknowledged_at'       => 'datetime',
        'dispatched_at'         => 'datetime',
        'started_at'            => 'datetime',
        'resolved_at'           => 'datetime',
        'closed_at'             => 'datetime',
        'sla_response_due_at'   => 'datetime',
        'sla_resolution_due_at' => 'datetime',
        'dtc_codes'             => 'array',
        'latitude'              => 'float',
        'longitude'             => 'float',
        'odometer'              => 'integer',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'is_overdue',
        'is_critical',
        'response_time',
        'resolution_time',
    ];

    // =====================================
    // CONSTANTS
    // =====================================

    /**
     * Available incident severities
     */
    const SEVERITY_LOW = 'Low';
    const SEVERITY_MEDIUM = 'Medium';
    const SEVERITY_HIGH = 'High';
    const SEVERITY_CRITICAL = 'Critical';

    /**
     * Available incident statuses
     */
    const STATUS_OPEN = 'Open';
    const STATUS_ACKNOWLEDGED = 'Acknowledged';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_RESOLVED = 'Resolved';
    const STATUS_CLOSED = 'Closed';

    /**
     * Available incident categories
     */
    const CATEGORIES = [
        'Engine',
        'Electrical',
        'Brakes',
        'Tires',
        'Transmission',
        'Fuel System',
        'Cooling System',
        'Exhaust',
        'Suspension',
        'Body/Interior',
        'Other',
    ];

    // =====================================
    // RELATIONSHIPS
    // =====================================

    /**
     * Get the user who reported this incident.
     *
     * @return BelongsTo
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    /**
     * Get the technician assigned to this incident.
     *
     * @return BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get the vehicle associated with this incident.
     *
     * @return BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_identifier', 'vehicle_number');
    }

    /**
     * Get all updates/comments for this incident.
     *
     * @return HasMany
     */
    public function updates(): HasMany
    {
        return $this->hasMany(IncidentUpdate::class)->orderBy('created_at', 'desc');
    }

    // =====================================
    // ACCESSORS & MUTATORS
    // =====================================

    /**
     * Check if the incident is overdue based on SLA.
     *
     * @return bool
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === self::STATUS_CLOSED) {
            return false;
        }

        if ($this->sla_resolution_due_at) {
            return Carbon::now()->isAfter($this->sla_resolution_due_at);
        }

        return false;
    }

    /**
     * Check if the incident is critical severity.
     *
     * @return bool
     */
    public function getIsCriticalAttribute(): bool
    {
        return $this->severity === self::SEVERITY_CRITICAL;
    }

    /**
     * Get the response time in hours.
     *
     * @return float|null
     */
    public function getResponseTimeAttribute(): ?float
    {
        if ($this->acknowledged_at && $this->created_at) {
            return $this->created_at->diffInHours($this->acknowledged_at);
        }

        return null;
    }

    /**
     * Get the resolution time in hours.
     *
     * @return float|null
     */
    public function getResolutionTimeAttribute(): ?float
    {
        if ($this->resolved_at && $this->created_at) {
            return $this->created_at->diffInHours($this->resolved_at);
        }

        return null;
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Scope a query to only include open incidents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope a query to only include unassigned incidents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to_user_id');
    }

    /**
     * Scope a query to only include critical incidents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', self::SEVERITY_CRITICAL);
    }

    /**
     * Scope a query to only include overdue incidents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', self::STATUS_CLOSED)
                     ->where('sla_resolution_due_at', '<', Carbon::now());
    }

    /**
     * Scope a query to filter by technician.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $technicianId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedTo($query, $technicianId)
    {
        return $query->where('assigned_to_user_id', $technicianId);
    }

    /**
     * Scope a query to filter by reporter.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $reporterId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReportedBy($query, $reporterId)
    {
        return $query->where('reported_by_user_id', $reporterId);
    }

    // =====================================
    // METHODS
    // =====================================

    /**
     * Assign the incident to a technician.
     *
     * @param User $technician
     * @return bool
     */
    public function assignTo(User $technician): bool
    {
        $this->assigned_to_user_id = $technician->id;
        $this->dispatched_at = Carbon::now();
        
        if ($this->status === self::STATUS_OPEN) {
            $this->status = self::STATUS_ACKNOWLEDGED;
            $this->acknowledged_at = Carbon::now();
        }
        
        return $this->save();
    }

    /**
     * Mark the incident as started.
     *
     * @return bool
     */
    public function markAsStarted(): bool
    {
        $this->status = self::STATUS_IN_PROGRESS;
        $this->started_at = Carbon::now();
        
        return $this->save();
    }

    /**
     * Mark the incident as resolved.
     *
     * @return bool
     */
    public function markAsResolved(): bool
    {
        $this->status = self::STATUS_RESOLVED;
        $this->resolved_at = Carbon::now();
        
        return $this->save();
    }

    /**
     * Close the incident.
     *
     * @return bool
     */
    public function close(): bool
    {
        $this->status = self::STATUS_CLOSED;
        $this->closed_at = Carbon::now();
        
        return $this->save();
    }

    /**
     * Add a comment/update to the incident.
     *
     * @param string $comment
     * @param User $user
     * @param string $type
     * @return IncidentUpdate|null
     */
    public function addComment(string $comment, User $user, string $type = 'comment')
    {
        return $this->updates()->create([
            'user_id' => $user->id,
            'type' => $type,
            'body' => $comment,
        ]);
    }

    /**
     * Get severity badge color for UI.
     *
     * @return string
     */
    public function getSeverityColor(): string
    {
        return match($this->severity) {
            self::SEVERITY_CRITICAL => 'danger',
            self::SEVERITY_HIGH => 'warning',
            self::SEVERITY_MEDIUM => 'info',
            self::SEVERITY_LOW => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get status badge color for UI.
     *
     * @return string
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'primary',
            self::STATUS_ACKNOWLEDGED => 'info',
            self::STATUS_IN_PROGRESS => 'warning',
            self::STATUS_RESOLVED => 'success',
            self::STATUS_CLOSED => 'secondary',
            default => 'light',
        };
    }

    /**
     * Calculate SLA compliance percentage.
     *
     * @return float
     */
    public function getSlaCompliancePercentage(): float
    {
        if (!$this->sla_resolution_due_at) {
            return 100.0;
        }

        $totalTime = $this->created_at->diffInMinutes($this->sla_resolution_due_at);
        $elapsedTime = $this->created_at->diffInMinutes(Carbon::now());
        
        if ($this->resolved_at) {
            $elapsedTime = $this->created_at->diffInMinutes($this->resolved_at);
        }
        
        $percentage = 100 - (($elapsedTime / $totalTime) * 100);
        
        return max(0, min(100, $percentage));
    }

    /**
     * Get all available severities.
     *
     * @return array
     */
    public static function getSeverities(): array
    {
        return [
            self::SEVERITY_LOW,
            self::SEVERITY_MEDIUM,
            self::SEVERITY_HIGH,
            self::SEVERITY_CRITICAL,
        ];
    }

    /**
     * Get all available statuses.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_ACKNOWLEDGED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_RESOLVED,
            self::STATUS_CLOSED,
        ];
    }

    /**
     * Get priority level based on severity.
     *
     * @return int
     */
    public function getPriorityLevel(): int
    {
        return match($this->severity) {
            self::SEVERITY_CRITICAL => 1,
            self::SEVERITY_HIGH => 2,
            self::SEVERITY_MEDIUM => 3,
            self::SEVERITY_LOW => 4,
            default => 5,
        };
    }

    /**
     * Check if incident can be assigned.
     *
     * @return bool
     */
    public function canBeAssigned(): bool
    {
        return in_array($this->status, [
            self::STATUS_OPEN,
            self::STATUS_ACKNOWLEDGED
        ]);
    }

    /**
     * Check if incident can be closed.
     *
     * @return bool
     */
    public function canBeClosed(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    /**
     * Get formatted location string.
     *
     * @return string|null
     */
    public function getFormattedLocation(): ?string
    {
        if ($this->location_description) {
            return $this->location_description;
        }

        if ($this->latitude && $this->longitude) {
            return sprintf("%.6f, %.6f", $this->latitude, $this->longitude);
        }

        return null;
    }

    /**
     * Get Google Maps URL for the incident location.
     *
     * @return string|null
     */
    public function getGoogleMapsUrl(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return sprintf(
                "https://maps.google.com/?q=%f,%f",
                $this->latitude,
                $this->longitude
            );
        }

        return null;
    }

    /**
     * Calculate estimated resolution time based on severity.
     *
     * @return int Hours
     */
    public function getEstimatedResolutionHours(): int
    {
        return match($this->severity) {
            self::SEVERITY_CRITICAL => 2,
            self::SEVERITY_HIGH => 4,
            self::SEVERITY_MEDIUM => 8,
            self::SEVERITY_LOW => 24,
            default => 48,
        };
    }

    /**
     * Get incident age in a human-readable format.
     *
     * @return string
     */
    public function getAge(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if the incident is new (less than 24 hours old).
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->created_at->isAfter(Carbon::now()->subDay());
    }

    /**
     * Get statistics for the incident.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'age_hours' => $this->created_at->diffInHours(Carbon::now()),
            'response_time' => $this->response_time,
            'resolution_time' => $this->resolution_time,
            'update_count' => $this->updates()->count(),
            'is_overdue' => $this->is_overdue,
            'sla_compliance' => $this->getSlaCompliancePercentage(),
        ];
    }
}