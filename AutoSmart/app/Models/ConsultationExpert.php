<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultationExpert extends Model
{
    protected $fillable = [
        'user_id', 'specialty', 'bio', 'certifications', 'experience_years',
        'hourly_rate', 'rating', 'total_sessions', 'available_hours', 'is_active'
    ];

    protected $casts = [
        'certifications' => 'array', 'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2', 'available_hours' => 'array', 'is_active' => 'boolean'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function sessions(): HasMany { return $this->hasMany(ConsultationSession::class, 'expert_id', 'user_id'); }

    public function scopeActive($query) { return $query->where('is_active', true); }
}
