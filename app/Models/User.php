<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Vehicle;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','vehicle_id','profile_picture'];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the vehicle assigned to the user (for drivers)
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_number');
    }

    /**
     * Get the vehicle assigned to this driver (alternative relationship name)
     */
    public function assignedVehicle()
    {
        return $this->hasOne(Vehicle::class, 'driver_id');
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        
        // Return default avatar based on role
        $defaultAvatars = [
            'Admin' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc2626&color=fff',
            'Driver' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2563eb&color=fff',
            'Technician' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=059669&color=fff',
        ];
        
        return $defaultAvatars[$this->role] ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6b7280&color=fff';
    }

    /**
     * Get user's sent messages
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get user's received messages
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get user's preferences
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get user's created announcements
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Get unread messages count
     */
    public function getUnreadMessagesCountAttribute()
    {
        try {
            return $this->receivedMessages()->where('is_read', false)->count();
        } catch (\Exception $e) {
            // Return 0 if table doesn't exist yet
            return 0;
        }
    }

    /**
     * Check if user has unread notifications
     */
    public function hasUnreadNotifications(): bool
    {
        return $this->unreadNotifications()->exists();
    }

    /**
     * Get user's incidents (for drivers)
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'reported_by_user_id');
    }

    /**
     * Get user's assigned incidents (for technicians)
     */
    public function assignedIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigned_to_user_id');
    }

    /**
     * Get user's service requests (as requester)
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'requester_id');
    }

    /**
     * Get user's assigned service requests (for technicians)
     */
    public function assignedRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_to');
    }

    /**
     * Get user's incident reports (as reporter)
     */
    public function incidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'reported_by');
    }

    /**
     * Get user's incident reports as driver
     */
    public function driverIncidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'driver_id');
    }

    /**
     * Get user's route assignments (for drivers)
     */
    public function routeAssignments(): HasMany
    {
        return $this->hasMany(RouteAssignment::class, 'driver_id');
    }

    /**
     * Get user's created route assignments (for admins)
     */
    public function createdRouteAssignments(): HasMany
    {
        return $this->hasMany(RouteAssignment::class, 'assigned_by');
    }

    /**
     * Get user's performance metrics (for drivers)
     */
    public function driverPerformanceMetrics(): HasMany
    {
        return $this->hasMany(DriverPerformanceMetric::class, 'driver_id');
    }

    /**
     * Get user's created routes (for admins)
     */
    public function createdRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'created_by');
    }

    /**
     * Get current performance score for driver
     */
    public function getCurrentPerformanceScore()
    {
        if ($this->role !== 'Driver') {
            return null;
        }

        $latestMetric = $this->driverPerformanceMetrics()
            ->latest('metric_date')
            ->first();

        return $latestMetric ? $latestMetric->overall_performance_score : 0;
    }

    /**
     * Get driver's active route assignments
     */
    public function getActiveRouteAssignments()
    {
        return $this->routeAssignments()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->with(['route', 'vehicle'])
            ->get();
    }

    public function resolvedAssignedVehicle(): ?Vehicle
{
    // 1) Canonical link: vehicles.driver_id = users.id
    $veh = Vehicle::where('driver_id', $this->id)->first();
    if ($veh) return $veh;

    // 2) users.vehicle_id might be vehicles.id (numeric)
    if (!empty($this->vehicle_id) && ctype_digit((string)$this->vehicle_id)) {
        $veh = Vehicle::find($this->vehicle_id);
        if ($veh) return $veh;
    }

    // 3) users.vehicle_id might be vehicles.vehicle_number (string)
    if (!empty($this->vehicle_id)) {
        $veh = Vehicle::where('vehicle_number', $this->vehicle_id)->first();
        if ($veh) return $veh;
    }

    // 4) Fallback: use the driver's active route assignment, if any
    $activeAssignment = $this->routeAssignments()
        ->whereIn('status', ['assigned', 'in_progress'])
        ->with('vehicle')
        ->latest()
        ->first();

    return $activeAssignment?->vehicle;
}

}
