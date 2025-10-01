<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentUpdate extends Model
{
    use HasFactory;

    protected $fillable = ['incident_id','user_id','type','body','data'];

    protected $casts = ['data' => 'array'];

    public function incident() { return $this->belongsTo(Incident::class); }
    public function user()     { return $this->belongsTo(User::class); }
}
