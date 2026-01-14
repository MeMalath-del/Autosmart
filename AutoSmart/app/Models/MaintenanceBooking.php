<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceBooking extends Model
{
    protected $fillable = [
        'maintenance_request_id', 'maintenance_quote_id', 'user_id', 'workshop_id',
        'workshop_service_id', 'booking_number', 'booking_date', 'booking_time',
        'notes', 'status', 'estimated_cost', 'final_cost',
    ];

    protected $casts = ['booking_date' => 'date', 'estimated_cost' => 'decimal:2', 'final_cost' => 'decimal:2'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($b) => $b->booking_number = $b->booking_number ?? 'BK-'.strtoupper(uniqid()));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(WorkshopService::class, 'workshop_service_id');
    }

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(MaintenanceQuote::class, 'maintenance_quote_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'قيد الانتظار', 'confirmed' => 'مؤكد', 'in_progress' => 'جاري التنفيذ',
            'completed' => 'مكتمل', 'cancelled' => 'ملغي', default => $this->status
        };
    }
}
